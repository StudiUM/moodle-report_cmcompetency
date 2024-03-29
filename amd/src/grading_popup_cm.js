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
 * Module to enable inline editing of a comptency grade for a course module.
 *
 * @module     report_cmcompetency/grading_popup_cm
 * @copyright  2019 Université de Montréal
 * @author     Marie-Eve Lévesque <marie-eve.levesque.8@umontreal.ca>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery', 'core/notification', 'core/str', 'core/ajax', 'core/log', 'core/templates',
    'core/modal_factory', 'core/modal_events'],
    function($, notification, str, ajax, log, templates, ModalFactory, ModalEvents) {

        /**
         * GradingPopupCm
         *
         * @param {String} regionSelector The regionSelector
         * @param {String} userCompetencySelector The userCompetencySelector
         * @param {Number} contextid The context id
         */
        var GradingPopupCm = function(regionSelector, userCompetencySelector, contextid) {
            this._regionSelector = regionSelector;
            this._userCompetencySelector = userCompetencySelector;
            this.contextid = contextid;
            $(this._regionSelector).on('click', this._userCompetencySelector, this._handleClick.bind(this));
        };

        /**
         * Get the data from the clicked cell and open the popup.
         *
         * @method _handleClick
         * @param {Event} e The event
         */
        GradingPopupCm.prototype._handleClick = function(e) {
            var cell = $(e.target).closest(this._userCompetencySelector);
            var competencyId = $(cell).data('competencyid');
            var cmId = $(cell).data('cmid');
            var userId = $(cell).data('userid');

            log.debug('Clicked on cell: competencyId=' + competencyId + ', cmId=' + cmId + ', userId=' + userId);

            var requests = ajax.call([{
                methodname: 'tool_cmcompetency_data_for_user_competency_summary_in_coursemodule',
                args: {userid: userId, competencyid: competencyId, cmid: cmId},
            }, {
                methodname: 'tool_cmcompetency_user_competency_viewed_in_coursemodule',
                args: {userid: userId, competencyid: competencyId, cmid: cmId},
            }]);

            $.when.apply($, requests).then(function(context) {
                this._contextLoaded.bind(this)(context, cell);
                return;
            }.bind(this)).catch(notification.exception);
        };

        /**
         * We loaded the context, now render the template.
         *
         * @method _contextLoaded
         * @param {Object} context
         * @param {Object} cell
         */
        GradingPopupCm.prototype._contextLoaded = function(context, cell) {
            var self = this;
            // We have to display user info in popup.
            context.displayuser = true;
            context.contextid = self.contextid;
            return str.get_string('usercompetencysummary', 'report_competency').done(function(title) {
                    return ModalFactory.create({
                        type: ModalFactory.types.DEFAULT,
                        title: title,
                        body: templates.render('report_cmcompetency/user_competency_summary_in_coursemodule', context),
                        large: true
                    }, cell).done(function(modal) {
                        // Keep a reference to the modal.
                        self.popup = modal;
                        self.popup.getRoot().on(ModalEvents.hidden, self._refresh.bind(self));
                        self.popup.show();
                    }.bind(this));
            }).fail(notification.exception);

        };

        /**
         * Get the data to refresh the page when the popup is closed.
         *
         * @method _refresh
         */
        GradingPopupCm.prototype._refresh = function() {
            var region = $(this._regionSelector);
            var cmId = region.data('cmid');
            var userId = region.data('userid');
            this.popup.destroy();
            ajax.call([{
                methodname: 'report_cmcompetency_data_for_report',
                args: {cmid: cmId, userid: userId},
                done: this._pageContextLoaded.bind(this),
                fail: notification.exception
            }]);
        };

        /**
         * We loaded the context, now render the template (refresh the page when the popup is closed).
         *
         * @method _pageContextLoaded
         * @param {Object} context
         */
        GradingPopupCm.prototype._pageContextLoaded = function(context) {
            var self = this;
            context.contextid = self.contextid;
            templates.render('report_cmcompetency/report', context).done(function(html, js) {
                templates.replaceNode(self._regionSelector, html, js);
            }).fail(notification.exception);
        };

        /** @type {String} The selector for the region with the user competencies */
        GradingPopupCm.prototype._regionSelector = null;

        /** @type {String} The selector for the region with a single user competencies */
        GradingPopupCm.prototype._userCompetencySelector = null;

        /** @type {Number} The context id */
        GradingPopupCm.prototype.contextid = null;

        /** @var {Dialogue} popup  The popup window (Dialogue). */
        GradingPopupCm.prototype.popup = null;

        return GradingPopupCm;
    });
