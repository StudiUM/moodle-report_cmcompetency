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
 * Renderer class for report_cmcompetency
 *
 * @package    report_cmcompetency
 * @copyright  2019 Université de Montréal
 * @author     Issam Taboubi <issam.taboubi@umontreal.ca>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace report_cmcompetency\output;

use plugin_renderer_base;
use renderable;

/**
 * Renderer class for course module competency report.
 *
 * @package    report_cmcompetency
 * @copyright  2019 Université de Montréal
 * @author     Issam Taboubi <issam.taboubi@umontreal.ca>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class renderer extends plugin_renderer_base {

    /**
     * Defer to template.
     *
     * @param report $page
     * @return string html for the page
     */
    public function render_report(report $page) {
        $data = $page->export_for_template($this);
        return parent::render_from_template('report_cmcompetency/report', $data);
    }

    /**
     * Defer to template.
     *
     * @param user_coursemodule_navigation $nav
     * @return string
     */
    public function render_user_coursemodule_navigation(user_coursemodule_navigation $nav) {
        $data = $nav->export_for_template($this);
        return parent::render_from_template('report_cmcompetency/user_coursemodule_navigation', $data);
    }

    /**
     * Defer to template.
     *
     * @param default_values_ratings $page
     * @return string html for the page
     */
    public function render_default_values_ratings(default_values_ratings $page) {
        $data = $page->export_for_template($this);
        return parent::render_from_template('report_cmcompetency/bulk_rating', $data);
    }

    /**
     * Output a notification.
     *
     * @param string $message the message to print out
     * @return string HTML fragment.
     * @see \core\output\notification
     */
    public function notify_message($message) {
        $n = new \core\output\notification($message, \core\output\notification::NOTIFY_INFO);
        return $this->render($n);
    }

    /**
     * Output an error notification.
     *
     * @param string $message the message to print out
     * @return string HTML fragment.
     * @see \core\output\notification
     */
    public function notify_problem($message) {
        $n = new \core\output\notification($message, \core\output\notification::NOTIFY_ERROR);
        return $this->render($n);
    }

    /**
     * Output a success notification.
     *
     * @param string $message the message to print out
     * @return string HTML fragment.
     * @see \core\output\notification
     */
    public function notify_success($message) {
        $n = new \core\output\notification($message, \core\output\notification::NOTIFY_SUCCESS);
        return $this->render($n);
    }
}
