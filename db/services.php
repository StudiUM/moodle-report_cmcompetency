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
 * Course module competency report webservice functions.
 *
 * @package    report_cmcompetency
 * @author     Issam Taboubi
 * @copyright  2019 Université de Montréal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$functions = [
    'report_cmcompetency_data_for_report' => [
        'classname'    => 'report_cmcompetency\external',
        'methodname'   => 'data_for_report',
        'classpath'    => '',
        'description'  => 'Load the data for the competency report in a course module.',
        'type'         => 'read',
        'capabilities' => 'moodle/competency:coursecompetencyview',
        'ajax'         => true,
    ],
    'report_cmcompetency_add_rating_task' => [
        'classname'    => 'report_cmcompetency\external',
        'methodname'   => 'add_rating_task',
        'classpath'    => '',
        'description'  => 'Add task for rating competencies in course modules',
        'type'         => 'write',
        'capabilities' => 'moodle/competency:competencygrade',
        'ajax'         => true,
    ],
];
