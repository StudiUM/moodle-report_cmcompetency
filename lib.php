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
 * This page contains navigation hooks for course module competency report.
 *
 * @package    report_cmcompetency
 * @author     Issam Taboubi <issam.taboubi@umontreal.ca>
 * @copyright  2019 Université de Montréal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
use core_competency\course_module_competency;

/**
 * This function extends the module navigation with the report items
 *
 * @param navigation_node $navigation The navigation node to extend
 * @param stdClass $cm
 */
function report_cmcompetency_extend_navigation_module($navigation, $cm) {
    $competencylist = course_module_competency::list_competencies($cm->id);
    if (has_capability('moodle/competency:competencygrade', context_course::instance($cm->course)) && !empty($competencylist)) {
        $url = new moodle_url('/report/cmcompetency/index.php', array('id' => $cm->id));
        $navigation->add(get_string('competenciesassessment', 'report_cmcompetency'), $url,
                navigation_node::TYPE_SETTING, null, 'cmcompetencyreport');
    }
}

