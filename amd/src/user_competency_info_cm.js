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
 * Module to refresh a user competency summary for a course module in a page.
 *
 * @package    report_cmcompetency
 * @copyright  2019 Université de Montréal
 * @author     Marie-Eve Lévesque <marie-eve.levesque.8@umontreal.ca>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery', 'core/notification', 'core/ajax', 'core/templates'], function($, notification, ajax, templates) {

    /**
     * InfoCm
     *
     * @param {JQuery} rootElement Selector to replace when the information needs updating.
     * @param {Number} competencyId The id of the competency.
     * @param {Number} userId The id of the user.
     * @param {Number} coursemoduleId The id of the course module.
     * @param {Boolean} displayuser If we should display the user info.
     * @param {Number} contextid The context id.
     */
    var InfoCm = function(rootElement, competencyId, userId, coursemoduleId, displayuser, contextid) {
        this._rootElement = rootElement;
        this._competencyId = competencyId;
        this._userId = userId;
        this.contextid = contextid;
        this._coursemoduleId = coursemoduleId;
        this._valid = true;
        this._displayuser = (typeof displayuser !== 'undefined') ? displayuser : false;

        this._methodName = 'tool_cmcompetency_data_for_user_competency_summary_in_coursemodule';
        this._args = {userid: this._userId, competencyid: this._competencyId, cmid: this._coursemoduleId};
        this._templateName = 'report_cmcompetency/user_competency_summary_in_coursemodule';
    };

    /**
     * Reload the info for this user competency.
     *
     * @method reload
     */
    InfoCm.prototype.reload = function() {
        var self = this,
            promises = [];

        if (!this._valid) {
            return;
        }

        promises = ajax.call([{
            methodname: this._methodName,
            args: this._args
        }]);

        promises[0].done(function(context) {
            // Check if we should also the user info.
            if (self._displayuser) {
                context.displayuser = true;
            }
            context.contextid = self.contextid;
            templates.render(self._templateName, context).done(function(html, js) {
                templates.replaceNode(self._rootElement, html, js);
            }).fail(notification.exception);
        }).fail(notification.exception);
    };

    /** @type {JQuery} The root element to replace in the DOM. */
    InfoCm.prototype._rootElement = null;
    /** @type {Number} The id of the course module. */
    InfoCm.prototype._coursemoduleId = null;
    /** @type {Boolean} Is this module valid? */
    InfoCm.prototype._valid = null;
    /** @type {Number} The id of the competency. */
    InfoCm.prototype._competencyId = null;
    /** @type {Number} The id of the user. */
    InfoCm.prototype._userId = null;
    /** @type {String} The method name to load the data. */
    InfoCm.prototype._methodName = null;
    /** @type {Object} The arguments to load the data. */
    InfoCm.prototype._args = null;
    /** @type {String} The template to reload the fragment. */
    InfoCm.prototype._templateName = null;
    /** @type {Boolean} If we should display the user info? */
    InfoCm.prototype._displayuser = false;
    /** @type {Number} The context id. */
    InfoCm.prototype.contextid = null;

    return InfoCm;

});
