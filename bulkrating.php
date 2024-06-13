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
 * This page allows teachers to rate all students in course module.
 *
 * @package    report_cmcompetency
 * @copyright  2019 Université de Montréal
 * @author     Issam Taboubi <issam.taboubi@umontreal.ca>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
global $USER;

$currentcmid = optional_param('id', null, PARAM_INT);
$courseid = required_param('courseid', PARAM_INT);
$course = $DB->get_record('course', ['id' => $courseid], '*', MUST_EXIST);
$context = context_course::instance($course->id);
$cmwithnocomp = false;

// Fetch current course module.
$cmids = \report_cmcompetency\api::get_list_course_modules_with_competencies($course->id);
if (empty($cmids)) {
    $currentcmid = -1;
} else {
    if (empty($currentcmid)) {
        $currentcmid = reset($cmids);
    } else if (!in_array($currentcmid, $cmids)) {
        $cmwithnocomp = true;
    }
}
require_login($course);
if ($currentcmid > 0) {
    $modinfo = get_fast_modinfo($course->id);
    $cm = get_coursemodule_from_id('', $currentcmid, 0, true);
    require_login($course, true, $cm);
    $PAGE->set_cm($cm);
}

$urlparams = ['id' => $currentcmid, 'courseid' => $courseid];
$url = new moodle_url('/report/cmcompetency/bulkrating.php', $urlparams);
$urlindex = new moodle_url('/report/cmcompetency/index.php', $urlparams);

$title = get_string('bulkdefaultrating', 'report_cmcompetency');

$PAGE->navigation->override_active_url($urlindex);
$PAGE->set_url($url);
$PAGE->set_title($title);
$coursename = format_string($course->fullname, true, ['context' => $context]);
$PAGE->set_heading($coursename);
$output = $PAGE->get_renderer('report_cmcompetency');

$outputnav = $PAGE->get_renderer('tool_cmcompetency');
echo $output->header();
if (has_capability('moodle/competency:competencygrade', $context)) {
    if ($currentcmid > 0) {
        $image = html_writer::empty_tag('img',
                        ['src' => $modinfo->cms[$currentcmid]->get_icon_url()->out(), 'class' => 'cm-competency-img']);

        echo $output->heading($image . format_string($cm->name), 2);
        echo $output->heading($title, 3);
        $baseurl = new moodle_url('/report/cmcompetency/bulkrating.php');
        $cmiddefault = ($cmwithnocomp) ? $currentcmid : null;
        $nav = new \tool_cmcompetency\output\coursemodule_navigation($cm, $course, $baseurl, $cmiddefault);
        if ($cmwithnocomp) {
            echo $output->container('', 'clearfix');
            echo $OUTPUT->notification(get_string('nocompetenciesincm', 'tool_cmcompetency'),
                    \core\output\notification::NOTIFY_INFO);
            echo $outputnav->render($nav);
        } else {
            // Group navigation if the activity has separated groups.
            $currentgroup = groups_get_activity_group($cm, true);
            if ($currentgroup !== false) {
                $groupselect = groups_print_activity_menu($cm, $PAGE->url, true);
                echo $output->container($groupselect, 'pull-left border p-2 mb-2');
            }
            echo $output->container('', 'clearfix');
            $exist = \report_cmcompetency\api::rating_task_exist($currentcmid, $currentgroup);
            if ($exist) {
                echo $OUTPUT->notification(get_string('taskratingrunning', 'report_cmcompetency'),
                    \core\output\notification::NOTIFY_WARNING);
            }
            echo $OUTPUT->notification(get_string('noticebulkrating', 'report_cmcompetency'),
                \core\output\notification::NOTIFY_INFO);
            echo $outputnav->render($nav);
            $report = new \report_cmcompetency\output\default_values_ratings($course->id, $currentcmid, $currentgroup);
            echo $output->render($report);
        }
    } else {
        echo $output->heading($title, 3);
        echo $output->container('', 'clearfix');
        echo $OUTPUT->notification(get_string('nocompetenciesincms', 'tool_cmcompetency'),
                \core\output\notification::NOTIFY_INFO);
    }
} else {
    echo $output->heading($title, 3);
    echo $output->container('', 'clearfix margin-notification');
    echo $OUTPUT->notification(get_string('cannotaccessreportpage', 'tool_cmcompetency'),
                \core\output\notification::NOTIFY_ERROR);
}

echo $OUTPUT->footer();
