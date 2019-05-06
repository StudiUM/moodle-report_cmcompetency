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
 * Course module competency report Task tests.
 *
 * @package   report_cmcompetency
 * @author    Issam Taboubi <issam.taboubi@umontreal.ca>
 * @copyright 2019 Université de Montréal
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/webservice/tests/helpers.php');

/**
 * Course module competency report Task tests.
 *
 * @package   report_cmcompetency
 * @author    Issam Taboubi <issam.taboubi@umontreal.ca>
 * @copyright 2019 Université de Montréal
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class report_cmcompetency_task_testcase extends externallib_advanced_testcase {

    /** @var stdClass $student1 User for generating plans, student of course1. */
    protected $student1 = null;

    /** @var stdClass $student2 User for generating plans, student of course1. */
    protected $student2 = null;

    /** @var stdClass $teacher1 teacher. */
    protected $teacher1 = null;

    /** @var stdClass $course1 Course that contains the activities to grade. */
    protected $course1 = null;

    /** @var stdClass $page Page for $course1. */
    protected $page = null;

    /** @var stdClass $framework Competency framework. */
    protected $framework = null;

    protected function setUp() {
        $this->resetAfterTest(true);
        $dg = $this->getDataGenerator();
        $lpg = $dg->get_plugin_generator('core_competency');
        $this->setAdminUser();

        $this->teacher1 = $dg->create_user();
        $this->student1 = $dg->create_user();
        $this->student2 = $dg->create_user();
        $this->course1 = $dg->create_course();

        $sysctx = context_system::instance();
        $c1ctx = context_course::instance($this->course1->id);

        $canviewucrole = $dg->create_role();
        assign_capability('moodle/competency:usercompetencyview', CAP_ALLOW, $canviewucrole, $sysctx->id);

        $cangraderole = $dg->create_role();
        assign_capability('moodle/competency:competencygrade', CAP_ALLOW, $cangraderole, $sysctx->id);

        // Give permission to view competencies.
        $dg->role_assign($canviewucrole, $this->teacher1->id, $c1ctx->id);
        // Give permission to rate.
        $dg->role_assign($cangraderole, $this->teacher1->id, $c1ctx->id);

        $pagegenerator = $this->getDataGenerator()->get_plugin_generator('mod_page');
        $this->page = $pagegenerator->create_instance(array('course' => $this->course1->id));

        $this->framework = $lpg->create_framework();

        // Enrol students in the course.
        $studentarch = get_archetype_roles('student');
        $studentrole = array_shift($studentarch);
        $coursecontext = context_course::instance($this->course1->id);
        $dg->role_assign($studentrole->id, $this->student1->id, $coursecontext->id);
        $dg->enrol_user($this->student1->id, $this->course1->id, $studentrole->id);
        $dg->role_assign($studentrole->id, $this->student2->id, $coursecontext->id);
        $dg->enrol_user($this->student2->id, $this->course1->id, $studentrole->id);
    }

    /*
     * Test add task.
     */
    public function test_add_rate_users_in_cm_task() {
        $cm = get_coursemodule_from_instance('page', $this->page->id);

        // Set current user to teacher.
        $this->setUser($this->teacher1);

        $data = [['compid' => 1, 'value' => 2], ['compid' => 2, 'value' => 3]];
        $data = json_encode($data);
        \report_cmcompetency\external::add_rating_task($cm->id, $data);
        $taskexist = \report_cmcompetency\api::rating_task_exist($cm->id);
        $this->assertTrue($taskexist);
        $tasks = \core\task\manager::get_adhoc_tasks('report_cmcompetency\task\rate_users_in_coursemodules');
        $task = reset($tasks);
        $cmdata = $task->get_custom_data();
        $this->assertEquals($cm->id, $cmdata->cms->cmid);
        $datascales = $cmdata->cms->scalevalues;
        $this->assertEquals(1, $datascales[0]->compid);
        $this->assertEquals(2, $datascales[0]->value);
        $this->assertEquals(2, $datascales[1]->compid);
        $this->assertEquals(3, $datascales[1]->value);
        // Test if save the same cours module ratings.
        try {
            \report_cmcompetency\external::add_rating_task($cm->id, $data);
            $this->fail('Must fail scales values ratings for course module already exist.');
        } catch (\Exception $ex) {
            $this->assertContains(get_string('taskratingrunning', 'report_cmcompetency'), $ex->getMessage());
        }

    }

    /*
     * Test execute_rate_users_in_cm_task.
     */
    public function test_execute_rate_users_in_cm_task() {
        $dg = $this->getDataGenerator();
        $cpg = $this->getDataGenerator()->get_plugin_generator('core_competency');
        $cm = get_coursemodule_from_instance('page', $this->page->id);

        // Create scales.
        $scale1 = $dg->create_scale(array('scale' => 'B,C,D', 'name' => 'scale 1'));
        $scaleconfig = array(array('scaleid' => $scale1->id));
        $scaleconfig[] = array('name' => 'B', 'id' => 1, 'scaledefault' => 1, 'proficient' => 0);
        $scaleconfig[] = array('name' => 'C', 'id' => 2, 'scaledefault' => 0, 'proficient' => 1);
        $scaleconfig[] = array('name' => 'D', 'id' => 3, 'scaledefault' => 0, 'proficient' => 1);

        $scale2 = $dg->create_scale(array('scale' => 'E,F,G,H', 'name' => 'scale 2'));
        $c2scaleconfig = array(array('scaleid' => $scale2->id));
        $c2scaleconfig[] = array('name' => 'E', 'id' => 1, 'scaledefault' => 0, 'proficient' => 0);
        $c2scaleconfig[] = array('name' => 'F', 'id' => 2, 'scaledefault' => 0, 'proficient' => 0);
        $c2scaleconfig[] = array('name' => 'G', 'id' => 3, 'scaledefault' => 0, 'proficient' => 1);
        $c2scaleconfig[] = array('name' => 'H', 'id' => 4, 'scaledefault' => 1, 'proficient' => 1);

        $framework = $cpg->create_framework(array(
            'scaleid' => $scale1->id,
            'scaleconfiguration' => $scaleconfig
        ));
        $c1 = $cpg->create_competency(array(
                    'competencyframeworkid' => $framework->get('id'),
                    'shortname' => 'c1',
                    'scaleid' => $scale2->id,
                    'scaleconfiguration' => $c2scaleconfig));
        $c2 = $cpg->create_competency(array('competencyframeworkid' => $framework->get('id'), 'shortname' => 'c2'));
        // Create some course competencies.
        $cpg->create_course_competency(array('competencyid' => $c1->get('id'), 'courseid' => $this->course1->id));
        $cpg->create_course_competency(array('competencyid' => $c2->get('id'), 'courseid' => $this->course1->id));

        // Link competencies to course modules.
        $cpg->create_course_module_competency(array('competencyid' => $c1->get('id'), 'cmid' => $cm->id));
        $cpg->create_course_module_competency(array('competencyid' => $c2->get('id'), 'cmid' => $cm->id));

        $datascales = [];
        $datascales = [['compid' => $c1->get('id'), 'value' => 4], ['compid' => $c2->get('id'), 'value' => 2]];
        $datascales = json_encode($datascales);
        // Set current user to teacher.
        $this->setUser($this->teacher1);
        \report_cmcompetency\external::add_rating_task($cm->id, $datascales);

        // Execute task.
        $this->setAdminUser();
        $tasks = \core\task\manager::get_adhoc_tasks('report_cmcompetency\task\rate_users_in_coursemodules');
        $task = reset($tasks);
        $task->execute();

        // Test user1 and user2 are rated in cmp1 and cmp2.
        $u1c1 = \tool_cmcompetency\api::get_user_competency_in_coursemodule($cm->id, $this->student1->id, $c1->get('id'));
        $u1c2 = \tool_cmcompetency\api::get_user_competency_in_coursemodule($cm->id, $this->student1->id, $c2->get('id'));
        $u2c1 = \tool_cmcompetency\api::get_user_competency_in_coursemodule($cm->id, $this->student2->id, $c1->get('id'));
        $u2c2 = \tool_cmcompetency\api::get_user_competency_in_coursemodule($cm->id, $this->student2->id, $c2->get('id'));
        $this->assertEquals(4, $u1c1->get('grade'));
        $this->assertEquals(2, $u1c2->get('grade'));
        $this->assertEquals(4, $u2c1->get('grade'));
        $this->assertEquals(2, $u2c2->get('grade'));
    }
}
