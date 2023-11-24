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
 * Apply default values for ratings in course module competency.
 *
 * @package    report_cmcompetency
 * @copyright  2019 Université de Montréal
 * @author     Issam Taboubi <issam.taboubi@umontreal.ca>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace report_cmcompetency\output;

use renderable;
use renderer_base;
use templatable;
use stdClass;

/**
 * Apply default values for ratings in course module competency.
 *
 * @package    report_cmcompetency
 * @copyright  2019 Université de Montréal
 * @author     Issam Taboubi <issam.taboubi@umontreal.ca>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class default_values_ratings implements renderable, templatable {

    /** @var int $courseid */
    protected $courseid;

    /** @var int $cmid */
    protected $cmid;

    /** @var int $group */
    protected $group;

    /**
     * Construct.
     *
     * @param int $courseid The course id
     * @param int $cmid The course module id
     * @param int $group The group id
     */
    public function __construct($courseid, $cmid, $group) {
        $this->courseid = $courseid;
        $this->cmid = $cmid;
        $this->group = $group;
    }

    /**
     * Export the data.
     *
     * @param renderer_base $output
     * @return stdClass
     */
    public function export_for_template(renderer_base $output) {
        $data = new stdClass();
        $comps = \core_competency\course_module_competency::list_competencies($this->cmid);
        $data->submitdisabled = \report_cmcompetency\api::rating_task_exist($this->cmid, $this->group);
        $data->hascompetencies = count($comps) == 0 ? 0 : 1;
        $data->courseid = $this->courseid;
        $data->cmid = $this->cmid;
        $datascalecompetencies = [];
        foreach ($comps as $comp) {
            $compscale = [];
            $compscale['compid'] = $comp->get('id');
            $compscale['compshortname'] = $comp->get('shortname');
            $compscale['idnumber'] = $comp->get('idnumber');
            $compscale['scaleid'] = $comp->get_scale()->id;
            $scale = $comp->get_scale();
            $scale->load_items();
            $scaleitems = $scale->scale_items;
            foreach ($scaleitems as $key => $name) {
                $s = [];
                $s['value'] = $key + 1;
                $s['name'] = $name;
                $s['proficient'] = $comp->get_proficiency_of_grade($key + 1);
                $s['default'] = $comp->get_default_grade()[0] == ($key + 1) ? 1 : 0;
                $compscale['scalevalues'][] = $s;
            }
            $datascalecompetencies[] = $compscale;
        }
        $data->datascalecompetencies = $datascalecompetencies;

        return $data;
    }
}
