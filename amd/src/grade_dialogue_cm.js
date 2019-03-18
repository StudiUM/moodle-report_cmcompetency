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
 * Grade dialogue for course modules.
 *
 * @package    report_cmcompetency
 * @copyright  2019 Université de Montréal
 * @author     Marie-Eve Lévesque <marie-eve.levesque.8@umontreal.ca>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery',
        'core/notification',
        'core/templates',
        'tool_lp/dialogue',
        'tool_lp/event_base',
        'core/str'
    ], function($, Notification, Templates, Dialogue, EventBase, Str) {

        /**
         * Grade Course module dialogue class.
         * @param {Array} ratingOptions
         */
        var GradeCm = function(ratingOptions, showApplyGroup) {
            EventBase.prototype.constructor.apply(this, []);
            this._ratingOptions = ratingOptions;
            this._showApplyGroup = showApplyGroup;
        };
        GradeCm.prototype = Object.create(EventBase.prototype);

        /** @type {Dialogue} The dialogue. */
        GradeCm.prototype._popup = null;
        /** @type {Array} Array of objects containing, 'value', 'name' and optionally 'selected'. */
        GradeCm.prototype._ratingOptions = null;

        /**
         * After render hook.
         *
         * @method _afterRender
         * @protected
         */
        GradeCm.prototype._afterRender = function() {
            var btnRate = this._find('[data-action="rate"]'),
                lstRating = this._find('[name="rating"]'),
                txtComment = this._find('[name="comment"]'),
                applyGroup = this._find('[name="applygroup"]');

            this._find('[data-action="cancel"]').click(function(e) {
                e.preventDefault();
                this._trigger('cancelled');
                this.close();
            }.bind(this));

            lstRating.change(function() {
                var node = $(this);
                if (!node.val()) {
                    btnRate.prop('disabled', true);
                } else {
                    btnRate.prop('disabled', false);
                }
            }).change();

            btnRate.click(function(e) {
                e.preventDefault();
                var val = lstRating.val();
                if (!val) {
                    return;
                }
                var valgroup = applyGroup.prop('checked');
                if( valgroup !== true ) {
                    valgroup = false;
                }
                this._trigger('rated', {
                    'rating': val,
                    'note': txtComment.val(),
                    'applygroup': valgroup
                });
                this.close();
            }.bind(this));
        };

        /**
         * Close the dialogue.
         *
         * @method close
         */
        GradeCm.prototype.close = function() {
            this._popup.close();
            this._popup = null;
        };

        /**
         * Opens the picker.
         *
         * @param {Number} competencyId The competency ID of the competency to work on.
         * @method display
         * @return {Promise}
         */
        GradeCm.prototype.display = function() {
            return this._render().then(function(html) {
                return Str.get_string('rate', 'tool_lp').then(function(title) {
                    this._popup = new Dialogue(
                        title,
                        html,
                        this._afterRender.bind(this)
                    );
                }.bind(this));
            }.bind(this)).fail(Notification.exception);
        };

        /**
         * Find a node in the dialogue.
         *
         * @param {String} selector
         * @method _find
         * @returns {node} The node
         * @protected
         */
        GradeCm.prototype._find = function(selector) {
            return $(this._popup.getContent()).find(selector);
        };

        /**
         * Render the dialogue.
         *
         * @method _render
         * @protected
         * @return {Promise}
         */
        GradeCm.prototype._render = function() {
            var context = {
                cangrade: this._canGrade,
                ratings: this._ratingOptions,
                showapplygroup: this._showApplyGroup
            };
            return Templates.render('report_cmcompetency/competency_grader_cm', context);
        };

        return GradeCm;

    });
