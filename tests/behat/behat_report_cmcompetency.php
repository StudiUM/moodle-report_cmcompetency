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
 * Step definition to generate database fixtures for learning plan report.
 *
 * @package    report_cmcompetency
 * @category   test
 * @author     Marie-Eve Lévesque <marie-eve.levesque.8@umontreal.ca>
 * @copyright  2020 Université de Montréal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../../lib/behat/behat_base.php');

use Behat\Mink\Exception\ElementNotFoundException as ElementNotFoundException;
use Behat\Mink\Exception\ExpectationException as ExpectationException;
use tool_cmcompetency\api;

/**
 * Step definition for learning plan report.
 *
 * @package    report_cmcompetency
 * @category   test
 * @author     Marie-Eve Lévesque <marie-eve.levesque.8@umontreal.ca>
 * @copyright  2020 Université de Montréal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_report_cmcompetency extends behat_base {
    /**
     * If rich text editor is not enabled, skip the test.
     *
     * @Given /^richtext editor is enabled$/
     */
    public function richtext_editor_is_enabled() {
        if (!api::show_richtext_editor()) {
            throw new \Moodle\BehatExtension\Exception\SkippedException;
        }
    }
    /**
     * If rich text editor is enabled, skip the test.
     *
     * @Given /^richtext editor is not enabled$/
     */
    public function richtext_editor_is_not_enabled() {
        if (api::show_richtext_editor()) {
            throw new \Moodle\BehatExtension\Exception\SkippedException;
        }
    }

    /**
     * Checks, that element of specified type is checked.
     * The 'cmcompetency' mention here is mandatory because some other plugins could define the same step.
     *
     * @Then /^the cmcompetency "(?P<element_string>(?:[^"]|\\")*)" "(?P<selector_string>[^"]*)" should be checked$/
     * @throws ExpectationException Thrown by behat_base::find
     * @param string $element Element we look in
     * @param string $selectortype The type of element where we are looking in.
     */
    public function the_cmcompetency_element_should_be_checked($element, $selectortype) {

        // Transforming from steps definitions selector/locator format to Mink format and getting the NodeElement.
        $node = $this->get_selected_node($selectortype, $element);

        if (!$node->hasAttribute('checked')) {
            throw new ExpectationException('The element "' . $element . '" is not checked', $this->getSession());
        }
    }
}
