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
 * This is the external API for cmcompetency report.
 *
 * @package    report_cmcompetency
 * @author     Issam Taboubi <issam.taboubi@umontreal.ca>
 * @copyright  2019 Université de Montréal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace report_cmcompetency;

use context_module;
use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_multiple_structure;
use core_external\external_single_structure;
use core_external\external_value;
use tool_cmcompetency\external\user_competency_cm_exporter;
use tool_lp\external\competency_summary_exporter;


/**
 * This is the external API for cmcompetency report.
 *
 * @author     Issam Taboubi <issam.taboubi@umontreal.ca>
 * @copyright  2019 Université de Montréal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class external extends external_api {
    /**
     * Returns description of data_for_report() parameters.
     *
     * @return \external_function_parameters
     */
    public static function data_for_report_parameters() {
        $cmid = new external_value(
            PARAM_INT,
            'The course module id',
            VALUE_REQUIRED
        );
        $userid = new external_value(
            PARAM_INT,
            'The user id',
            VALUE_REQUIRED
        );
        $params = [
            'cmid' => $cmid,
            'userid' => $userid,
        ];
        return new external_function_parameters($params);
    }

    /**
     * Loads the data required to render the report.
     *
     * @param int $cmid The course module id
     * @param int $userid The user id
     * @return \stdClass
     */
    public static function data_for_report($cmid, $userid) {
        global $PAGE;
        $params = self::validate_parameters(
            self::data_for_report_parameters(),
            [
                'cmid' => $cmid,
                'userid' => $userid,
            ]
        );
        $context = context_module::instance($params['cmid']);
        self::validate_context($context);
        if (!is_enrolled($context, $params['userid'], 'moodle/competency:coursecompetencygradable')) {
            throw new coding_exception('invaliduser');
        }

        $renderable = new output\report($params['cmid'], $params['userid']);
        $renderer = $PAGE->get_renderer('report_cmcompetency');

        $data = $renderable->export_for_template($renderer);
        return $data;
    }

    /**
     * Returns description of data_for_report() result value.
     *
     * @return external_single_structure
     */
    public static function data_for_report_returns() {
        return new external_single_structure([
            'userid' => new external_value(PARAM_INT, 'User id'),
            'cmid' => new external_value(PARAM_INT, 'Course module id'),
            'usercompetencies' => new external_multiple_structure(
                new external_single_structure([
                    'usercompetencycoursemodule' => user_competency_cm_exporter::get_read_structure(),
                    'competency' => competency_summary_exporter::get_read_structure(),
                ])
            ),
        ]);
    }

    /**
     * Returns description of add_rating_task() parameters.
     *
     * @return \external_function_parameters
     */
    public static function add_rating_task_parameters() {
        $cmid = new external_value(
            PARAM_INT,
            'The course module id',
            VALUE_REQUIRED
        );
        $defaultscalesvalues = new external_value(
            PARAM_RAW,
            'Default scales values',
            VALUE_REQUIRED
        );
        $group = new external_value(
            PARAM_INT,
            'The group id',
            VALUE_DEFAULT,
            0
        );
        $params = [
            'cmid' => $cmid,
            'defaultscalesvalues' => $defaultscalesvalues,
            'group' => $group,
        ];
        return new external_function_parameters($params);
    }

    /**
     * Add task for rating competencies in course modules.
     *
     * @param int $cmid The course module id
     * @param string $defaultscalesvalues Default scales values
     * @param int $group The group id (can be 0 if no groups)
     * @return boolean
     */
    public static function add_rating_task($cmid, $defaultscalesvalues, $group = 0) {
        $params = self::validate_parameters(
            self::add_rating_task_parameters(),
            [
                'cmid' => $cmid,
                'defaultscalesvalues' => $defaultscalesvalues,
                'group' => $group,
            ]
        );
        api::add_rating_task($params['cmid'], json_decode($params['defaultscalesvalues']), $params['group']);
        return true;
    }

    /**
     * Returns description of add_rating_task() result value.
     *
     * @return external_value
     */
    public static function add_rating_task_returns() {
        return new external_value(PARAM_BOOL, 'True if adding was successful');
    }
}
