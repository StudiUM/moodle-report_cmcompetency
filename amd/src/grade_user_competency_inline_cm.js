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
 * @package    report_cmcompetency
 * @copyright  2019 Université de Montréal
 * @author     Marie-Eve Lévesque <marie-eve.levesque.8@umontreal.ca>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery',
        'core/notification',
        'core/ajax',
        'core/log',
        'report_cmcompetency/grade_dialogue_cm',
        'tool_lp/event_base',
        'tool_lp/scalevalues'
    ], function($, notification, ajax, log, GradeDialogue, EventBase, ScaleValues) {

        /**
         * InlineEditorCm
         *
         * @param {String} selector The selector to trigger the grading.
         * @param {Number} scaleId The id of the scale for this competency.
         * @param {Number} competencyId The id of the competency.
         * @param {Number} userId The id of the user.
         * @param {Number} coursemoduleId The id of the course module.
         * @param {String} chooseStr Language string for choose a rating.
         * @param {Boolean} showApplyGroup True if the option to grade group should be visible.
         * @param {Number} contextid The context id.
         */
        var InlineEditorCm = function(selector, scaleId, competencyId, userId, coursemoduleId, chooseStr,
                showApplyGroup, contextid) {
            EventBase.prototype.constructor.apply(this, []);

            var trigger = $(selector);
            if (!trigger.length) {
                throw new Error('Could not find the trigger');
            }

            this._scaleId = scaleId;
            this._competencyId = competencyId;
            this._userId = userId;
            this._coursemoduleId = coursemoduleId;
            this._chooseStr = chooseStr;
            this._showApplyGroup = showApplyGroup;
            this.contextid = contextid;
            this._setUp();

            trigger.click(function(e) {
                e.preventDefault();
                this._dialogue.display();
            }.bind(this));

            this._methodName = 'tool_cmcompetency_grade_competency_in_coursemodule';
            this._args = {
                competencyid: this._competencyId,
                cmid: this._coursemoduleId,
                userid: this._userId
            };
        };
        InlineEditorCm.prototype = Object.create(EventBase.prototype);

        /**
         * Setup.
         *
         * @method _setUp
         */
        InlineEditorCm.prototype._setUp = function() {
            var options = [],
                self = this;

            var promise = ScaleValues.get_values(self._scaleId);
            promise.done(function(scalevalues) {
                options.push({
                    value: '',
                    name: self._chooseStr
                });

                for (var i = 0; i < scalevalues.length; i++) {
                    var optionConfig = scalevalues[i];
                    options.push({
                        value: optionConfig.id,
                        name: optionConfig.name
                    });
                }

                self._dialogue = new GradeDialogue(options, self._showApplyGroup, self.contextid);
                self._dialogue.on('rated', function(e, data) {
                    var args = self._args;
                    args.grade = data.rating;
                    args.note = self._dialogue._find('form').serialize();
                    args.applygroup = data.applygroup;
                    ajax.call([{
                        methodname: self._methodName,
                        args: args,
                        done: function(evidence) {
                            self._trigger('competencyupdated', {args: args, evidence: evidence});
                        },
                        fail: notification.exception
                    }]);
                });
            }).fail(notification.exception);
        };

        /** @type {Number} The scale id for this competency. */
        InlineEditorCm.prototype._scaleId = null;
        /** @type {Number} The id of the competency. */
        InlineEditorCm.prototype._competencyId = null;
        /** @type {Number} The id of the user. */
        InlineEditorCm.prototype._userId = null;
        /** @type {Number} The id of the course module. */
        InlineEditorCm.prototype._coursemoduleId = null;
        /** @type {String} The text for Choose rating. */
        InlineEditorCm.prototype._chooseStr = null;
        /** @type {GradeDialogue} The grading dialogue. */
        InlineEditorCm.prototype._dialogue = null;
        /** @type {Boolean} True if the option to grade group should be visible. */
        InlineEditorCm.prototype._showApplyGroup = false;
        /** @type {Number} The context id. */
        InlineEditorCm.prototype.contextid = null;

        return InlineEditorCm;
    });