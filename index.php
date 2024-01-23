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
 * This page lets teachers to rate competencies in course module.
 *
 * @package    report_cmcompetency
 * @copyright  2019 Université de Montréal
 * @author     Issam Taboubi <issam.taboubi@umontreal.ca>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');

$cmid = required_param('id', PARAM_INT);
$cm = get_coursemodule_from_id('', $cmid, 0, true, MUST_EXIST);

$params = ['id' => $cm->course];
$course = $DB->get_record('course', $params, '*', MUST_EXIST);
require_login($course);
$context = context_course::instance($course->id);
$currentuser = optional_param('user', null, PARAM_INT);

// Fetch current active group.
groups_get_activity_groupmode($cm);
$currentgroup = groups_get_activity_group($cm, true);

if (empty($currentuser)) {
    $gradable = \tool_cmcompetency\api::get_cm_gradable_users($context, $cm, $currentgroup, true);
    if (empty($gradable)) {
        $currentuser = 0;
    } else {
        $currentuser = array_pop($gradable)->id;
    }
} else {
    $gradable = \tool_cmcompetency\api::get_cm_gradable_users($context, $cm, $currentgroup, false);
    if (count($gradable) == 0) {
        $currentuser = 0;
    } else if (!in_array($currentuser, array_keys($gradable))) {
        $currentuser = array_shift($gradable)->id;
    }
}

$urlparams = ['id' => $cmid];
$navurl = new moodle_url('/report/cmcompetency/index.php', $urlparams);
$urlparams['user'] = $currentuser;
$url = new moodle_url('/report/cmcompetency/index.php', $urlparams);

$title = get_string('competenciesassessment', 'report_cmcompetency');
$PAGE->set_cm($cm);

$PAGE->navigation->override_active_url($navurl);
$PAGE->set_url($url);
$PAGE->set_title($title);
$coursename = format_string($course->fullname, true, ['context' => $context]);
$PAGE->set_heading($coursename);
$output = $PAGE->get_renderer('report_cmcompetency');
echo $output->header();

echo $output->heading(format_string($cm->name), 2);

$baseurl = new moodle_url('/report/cmcompetency/index.php');
$nav = new \report_cmcompetency\output\user_coursemodule_navigation($currentuser, $cm->id, $baseurl);
echo $output->render($nav);

if ($currentuser > 0) {
    $user = core_user::get_user($currentuser);
    $usercontext = context_user::instance($currentuser);
    $userheading = [
            'heading'     => fullname($user),
            'user'        => $user,
            'usercontext' => $usercontext,
        ];
    echo $output->context_header($userheading, 3);
}

echo $output->heading($title, 3);
if ($currentuser > 0) {
    $page = new \report_cmcompetency\output\report($cm->id, $currentuser);
    echo $output->render($page);
} else {
    echo $output->container('', 'clearfix');
    echo $output->notify_problem(get_string('noparticipants', 'tool_lp'));
}

echo $OUTPUT->footer();
