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
 * User navigation in course module competencies class.
 *
 * @package    report_cmcompetency
 * @copyright  2019 Université de Montréal
 * @author     Issam Taboubi <issam.taboubi@umontreal.ca>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace report_cmcompetency\output;
defined('MOODLE_INTERNAL') || die();

use renderable;
use renderer_base;
use templatable;
use context_course;
use core_user\external\user_summary_exporter;
use stdClass;

/**
 * User navigation in course module competencies class.
 *
 * @package    report_cmcompetency
 * @copyright  2019 Université de Montréal
 * @author     Issam Taboubi <issam.taboubi@umontreal.ca>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class user_coursemodule_navigation implements renderable, templatable {

    /** @var int $userid */
    protected $userid;

    /** @var int $cmid */
    protected $cmid;

    /** @var string $baseurl */
    protected $baseurl;

    /**
     * Construct.
     *
     * @param int $userid
     * @param int $cmid
     * @param string $baseurl
     */
    public function __construct($userid, $cmid, $baseurl) {
        $this->userid = $userid;
        $this->cmid = $cmid;
        $this->baseurl = $baseurl;
    }

    /**
     * Export the data.
     *
     * @param renderer_base $output
     * @return stdClass
     */
    public function export_for_template(renderer_base $output) {
        global $CFG, $PAGE, $OUTPUT;

        $data = new stdClass();
        $data->userid = $this->userid;
        $data->cmid = $this->cmid;
        $data->baseurl = $this->baseurl;
        $data->groupselector = '';
        $cm = get_coursemodule_from_id('', $this->cmid, 0, true, MUST_EXIST);
        $context = context_course::instance($cm->course);

        if (has_any_capability(array('moodle/competency:usercompetencyview', 'moodle/competency:coursecompetencymanage'),
                $context)) {
            $currentgroup = groups_get_activity_group($cm, true);
            if ($currentgroup !== false) {
                $select = groups_print_activity_menu($cm, $PAGE->url, true);
                $data->groupselector = $select;
            }

            // Fetch current active group.
            $groupmode = groups_get_activity_groupmode($cm);

            $users = \tool_cmcompetency\api::get_cm_gradable_users($context, $cm, $currentgroup, false);
            $data->users = array();
            $users = array_values($users);
            $data->nextuserurl = null;
            $data->previoususerurl = null;
            $urlparams = ['id' => $this->cmid];
            foreach ($users as $key => $user) {
                $exporter = new user_summary_exporter($user);
                $user = $exporter->export($output);
                if ($user->id == $this->userid) {
                    $user->selected = true;
                    if (isset($users[$key - 1])) {
                        $urlparams['user'] = $users[$key - 1]->id;
                        $data->previoususerurl = new \moodle_url('/report/cmcompetency/index.php', $urlparams);
                    }
                    if (isset($users[$key + 1])) {
                        $urlparams['user'] = $users[$key + 1]->id;
                        $data->nextuserurl = new \moodle_url('/report/cmcompetency/index.php', $urlparams);
                    }
                }
                $data->users[] = $user;
            }
            $data->hasusers = true;
            $urloptions = ['courseid' => $cm->course, 'id' => $cm->id];
            if (!empty($currentgroup)) {
                $urloptions['group'] = $currentgroup;
            }
            $ratingbtnurl = new \moodle_url('/report/cmcompetency/bulkrating.php', $urloptions);
            $data->btnbulkrating = $OUTPUT->single_button($ratingbtnurl,
                    get_string('bulkdefaultrating', 'report_cmcompetency'), 'get');
        } else {
            $data->users = array();
            $data->hasusers = false;
            $data->btnbulkrating = '';
        }

        return $data;
    }
}
