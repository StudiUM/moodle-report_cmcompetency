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
 * Class containing data for course module competency rating page.
 *
 * @package    report_cmcompetency
 * @copyright  2019 Université de Montréal
 * @author     Issam Taboubi <issam.taboubi@umontreal.ca>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace report_cmcompetency\output;

use renderable;
use templatable;
use renderer_base;
use stdClass;
use core_competency\api as core_competency_api;
use core_competency\external\performance_helper;
use core_user;
use context_module;
use tool_cmcompetency\api as tool_cmcompetency_api;
use tool_cmcompetency\external\user_competency_cm_exporter;
use tool_lp\external\competency_summary_exporter;

/**
 * Class containing data for course module competency rating page.
 *
 * @copyright  2019 Université de Montréal
 * @author     Issam Taboubi <issam.taboubi@umontreal.ca>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class report implements renderable, templatable {
    /** @var int $cmid */
    protected $cmid;
    /** @var int $userid */
    protected $userid;

    /**
     * Construct this renderable.
     *
     * @param int $cmid The course module id
     * @param int $userid The user id
     */
    public function __construct($cmid, $userid) {
        $this->cmid = $cmid;
        $this->userid = $userid;
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param \renderer_base $output
     * @return stdClass
     */
    public function export_for_template(renderer_base $output) {
        $data = new stdClass();
        $data->cmid = $this->cmid;
        $data->userid = $this->userid;

        $user = core_user::get_user($this->userid);
        $contextcm = context_module::instance($this->cmid);
        $data->contextid = $contextcm->id;

        $data->usercompetencies = [];
        $cmcompetencies = core_competency_api::list_course_module_competencies($this->cmid);
        $usercompetencycoursesmodules = tool_cmcompetency_api::list_user_competencies_in_coursemodule($this->cmid, $user->id);

        $helper = new performance_helper();
        foreach ($usercompetencycoursesmodules as $usercompetencycoursemodule) {
            $onerow = new stdClass();
            $competency = null;
            foreach ($cmcompetencies as $cmcompetency) {
                if ($cmcompetency['competency']->get('id') == $usercompetencycoursemodule->get('competencyid')) {
                    $competency = $cmcompetency['competency'];
                    break;
                }
            }
            if (!$competency) {
                continue;
            }

            $framework = $helper->get_framework_from_competency($competency);
            $scale = $helper->get_scale_from_competency($competency);

            $exporter = new user_competency_cm_exporter($usercompetencycoursemodule, ['scale' => $scale]);
            $record = $exporter->export($output);
            $onerow->usercompetencycoursemodule = $record;
            $exporter = new competency_summary_exporter(null, [
                'competency' => $competency,
                'framework' => $framework,
                'context' => $framework->get_context(),
                'relatedcompetencies' => [],
                'linkedcourses' => [],
            ]);
            $onerow->competency = $exporter->export($output);
            array_push($data->usercompetencies, $onerow);
        }

        return $data;
    }
}
