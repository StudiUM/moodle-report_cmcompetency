<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Class for doing things related to course module competencies report.
 *
 * @package    report_cmcompetency
 * @author     Issam Taboubi <issam.taboubi@umontreal.ca>
 * @copyright  2019 Université de Montréal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace report_cmcompetency;

/**
 * Class for doing things with report cmcompetency.
 *
 * @author     Issam Taboubi <issam.taboubi@umontreal.ca>
 * @copyright  2019 Université de Montréal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class api {

    /**
     * List course modules having at least one competency.
     *
     * @param int $courseid the course id
     * @return array Array of course module id
     */
    public static function get_list_course_modules_with_competencies($courseid) {
        global $DB;

        $params = ['course' => $courseid];
        $sql = 'SELECT DISTINCT(cm.id)
                  FROM {course_modules} cm
            RIGHT JOIN {' . \core_competency\course_module_competency::TABLE . '} cmcomp
                    ON cm.id = cmcomp.cmid
                 WHERE cm.course = :course
              ORDER BY cm.added ASC';

        $cmids = $DB->get_records_sql($sql, $params);
        return array_keys($cmids);
    }

    /**
     * Add rating task.
     *
     * @param int $cmid The course module id
     * @param string $scalesvalues json scale values
     * @param int $group The group id
     */
    public static function add_rating_task($cmid, $scalesvalues, $group = 0) {
        global $USER;
        $cm = get_coursemodule_from_id('', $cmid, 0, true);
        // Check if current user has capability to grade in course.
        $context = \context_course::instance($cm->course);
        if (!has_capability('moodle/competency:competencygrade', $context)) {
            throw new required_capability_exception($context, 'moodle/competency:competencygrade', 'nopermissions', '');
        }
        // Check if there is current task for this course module.
        if (self::rating_task_exist($cmid, $group)) {
            throw new \moodle_exception('taskratingrunning', 'report_cmcompetency');
        }

        // Build custom data for adhoc task.
        $customdata = [];
        $customdata['cmid'] = $cmid;
        $customdata['scalevalues'] = $scalesvalues;
        $customdata['group'] = $group;

        $task = new \report_cmcompetency\task\rate_users_in_coursemodules();
        $task->set_custom_data([
                    'cms' => $customdata,
                ]);
        $task->set_userid($USER->id);

        // Queue the task for the next run.
        \core\task\manager::queue_adhoc_task($task);
    }

    /**
     * Rate users in course module competencies with default scales values.
     *
     * @param object $compdata Competencies with scales values associated.
     */
    public static function rate_users_in_cm_with_defaultvalues($compdata) {
        if (isset($compdata->cms) && $compdata->cms->cmid) {
            $cmid = $compdata->cms->cmid;
            $cm = get_coursemodule_from_id('', $cmid, 0, true);
            $context = \context_course::instance($cm->course);
            $users = \tool_cmcompetency\api::get_cm_gradable_users($context, $cm, $compdata->cms->group);
            foreach ($users as $user) {
                foreach ($compdata->cms->scalevalues as $data) {
                    $ucc = \tool_cmcompetency\api::get_user_competency_in_coursemodule($cmid, $user->id, $data->compid);
                    if ($ucc->get('grade') === null) {
                        try {
                            \tool_cmcompetency\api::grade_competency_in_coursemodule($cmid, $user->id, $data->compid, $data->value);
                        } catch (\Exception $ex) {
                            mtrace($ex->getMessage());
                            continue;
                        }
                    }
                }
            }
        }
    }

    /**
     * Check if task exist for course module.
     *
     * @param int $cmid the course module id
     * @param int $group the group id (can be 0 if no groups)
     * @return boolean return true if task exist
     */
    public static function rating_task_exist($cmid, $group) {
        if (empty($group)) {
            $group = 0;
        }
        $exist = false;
        $tasks = \core\task\manager::get_adhoc_tasks('report_cmcompetency\task\rate_users_in_coursemodules');
        foreach ($tasks as $task) {
            $cmdata = $task->get_custom_data();
            if ($cmdata->cms && $cmdata->cms->cmid == $cmid) {
                // There is already a task for this specific group, or for the whole course,
                // or for a group when trying to make one for the whole course.
                if ($cmdata->cms->group == $group || ($group == 0 && $cmdata->cms->group != 0) ||
                        ($group != 0 && $cmdata->cms->group == 0)) {
                    $exist = true;
                    break;
                }
            }
        }
        return $exist;
    }
}
