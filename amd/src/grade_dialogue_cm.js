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
 * @module     report_cmcompetency/grade_dialogue_cm
 * @copyright  2019 Université de Montréal
 * @author     Marie-Eve Lévesque <marie-eve.levesque.8@umontreal.ca>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery',
        'core/notification',
        'core/templates',
        'core/modal_factory',
        'core/modal_events',
        'tool_lp/event_base',
        'core/str',
        'core/fragment'
    ], function($, Notification, Templates, ModalFactory, ModalEvents, EventBase, Str, Fragment) {

        /**
         * Grade Course module dialogue class.
         * @param {Array} ratingOptions
         * @param {Boolean} showApplyGroup
         * @param {Number} contextid
         * @param {object} trigger the element trigger
         */
        var GradeCm = function(ratingOptions, showApplyGroup, contextid, trigger) {
            EventBase.prototype.constructor.apply(this, []);
            this._ratingOptions = ratingOptions;
            this._showApplyGroup = showApplyGroup;
            this.contextid = contextid;
            this.display($(trigger));
        };
        GradeCm.prototype = Object.create(EventBase.prototype);

        /** @type {Dialogue} The dialogue. */
        GradeCm.prototype.modal = null;
        /** @type {Array} Array of objects containing, 'value', 'name' and optionally 'selected'. */
        GradeCm.prototype._ratingOptions = null;
        /** @type {Number} The context id. */
        GradeCm.prototype.contextid = null;

        /**
         * After render hook.
         *
         * @method _afterRender
         * @protected
         */
        GradeCm.prototype._afterRender = function() {
            var btnRate = this._find('[data-action="save"]'),
                lstRating = this._find('[name="rating"]'),
                applyGroup = this._find('[name="applygroup"]');

            this._find('[data-action="cancel"]').click(function(e) {
                e.preventDefault();
                this._trigger('cancelled');
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
                    'applygroup': valgroup
                });
            }.bind(this));
        };

        /**
         * destroy the dialogue.
         *
         * @method destroy
         */
        GradeCm.prototype.destroy = function() {
            this.modal.destroy();
        };

        /**
         * Opens the picker.
         *
         * @param {object} trigger the element trigger
         * @method display
         * @return {Promise}
         */
        GradeCm.prototype.display = function(trigger) {
                return Str.get_string('rate', 'tool_lp').then(function(title) {
                    return ModalFactory.create({
                        type: ModalFactory.types.SAVE_CANCEL,
                        title: title,
                        body: '',
                        large: true,
                        buttons: {
                            save: title,
                        }
                    }, trigger).done(function(modal) {
                        // Keep a reference to the modal.
                        this.modal = modal;
                        // We want to reset the form every time it is opened.
                        this.modal.getRoot().on(ModalEvents.shown, function() {
                            this.modal.setBody(this._render());
                        }.bind(this));

                        this.modal.getRoot().on(ModalEvents.hidden, function() {
                            this._trigger('popupdestroyed');
                            this.modal.destroy();
                        }.bind(this));

                        // We want to hide the submit buttons of the form every time it is opened.
                        this.modal.getRoot().on(ModalEvents.bodyRendered, function() {
                            this.modal.getRoot().find('[data-groupname=buttonar]').addClass('hidden');
                            this._afterRender();
                        }.bind(this));

                        return this.modal;
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
            return this.modal.getRoot().find(selector);
        };

        /**
         * Render the dialogue.
         *
         * @method _render
         * @protected
         * @return {Promise}
         */
        GradeCm.prototype._render = function() {
            var args = {};
            args.canGrade = (this._canGrade) ? true : false;
            args.ratingOptions = JSON.stringify(this._ratingOptions);
            args.showapplygroup = (this._showApplyGroup) ? true : false;
            args.contextid = this.contextid;

            return Fragment.loadFragment('tool_cmcompetency', 'grade_cm', this.contextid, args);
        };

        return GradeCm;

    });
