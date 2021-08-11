/* global toggleValueElements */
/* eslint-disable no-native-reassign */
define([
    'jquery',
    'knockout',
    'Swissup_ThemeEditor/lib/dragula/dragula',
    'mage/utils/wrapper',
    'Magento_Ui/js/modal/modal' // 2.3.3: create 'jquery-ui-modules/widget' dependency
], function ($, ko, dragula, wrapper, modal) {
    'use strict';

    /**
     * KO view model to maintain layout mockup
     *
     * @param {Object} configValue
     * @param {Object} availableBlocks
     */
    function DragulaElementViewModel(configValue, availableBlocks) {
        var self = this;

        self.availableBlocks = availableBlocks;

        self.containers = $.map($.parseJSON(configValue), function (element) {
            element.children = ko.observableArray(element.children);

            $.each(element.config, function(key, value) {
                element.config[key] = ko.observable(value);
            });

            return element;
        });

        /**
         * [removeChild description]
         */
        self.removeChild = function (block, container) {
            container.children.remove(block);
        };

        /**
         * [addChild description]
         */
        self.addChild = function (block, container, beforeBlock) {
            var index;

            if (beforeBlock) {
                index = container.children().indexOf(beforeBlock);
                container.children.splice(index, 0, block);
            } else {
                container.children.push(block);
            }
        };

        self.layoutConfigValue = ko.computed(function () {
            return ko.toJSON(self.containers);
        });

        /**
         * @param  {String} name container name
         */
        self.showConfig = function (name) {
            var curItem = $('[data-type="container"][data-name="' + name + '"] .settings-dropdown'),
                curState = curItem.hasClass('shown');

            $('[data-type="container"] .settings-dropdown').removeClass('shown');
            if (!curState) {
                curItem.addClass('shown');
            }
        };

        /**
         * @param  {HTMLElement} el
         * @return {Boolean}
         */
        self.isNameAvailable = function (el) {
            return $(el).data('name') === 'available';
        };

        /**
         * @param  {HTMLElement} el
         * @param  {HTMLElement} target
         * @param  {HTMLElement} source
         * @param  {HTMLElement} sibling
         */
        self.onDragulaDrop = function (el, target, source, sibling) {
            var block;

            if (!target) {
                return;
            }

            if (self.isNameAvailable(source)) {
                // create new ko data block
                block = {
                    'name': $(el).data('name')
                };
            } else {
                // remove block from old container
                block = ko.dataFor(el);
                self.removeChild(block, ko.dataFor(source));
            }

            // add block to new container
            if (!self.isNameAvailable(target)) {
                self.addChild(
                    block,
                    ko.dataFor(target),
                    sibling ? ko.dataFor(sibling) : null
                );

                // remove droped element to prevent block duplication
                $(el).remove();
            }
        };
    }

    /**
     * @param  {Object} options
     * @return {jQuery}
     */
    function prepareDragulaElement(options) {
        var dragulaElement = $(
            '<div/>',
            {
                class: 'dragula-element',
                'data-bind': 'template: { name: \'layout-template\', foreach: containers}'
            }
        );

        if (options.disabled) {
            dragulaElement.addClass('disabled');
        }

        return dragulaElement;
    }

    $.widget('swissup.headerLayout', {
        /**
         * {@inheritdoc}
         */
        _create: function () {
            var dragulaVM;

            this.configField = this.element
                .children('textarea')
                .attr('data-bind', 'value: layoutConfigValue')
                .hide();
            prepareDragulaElement(this.options).insertAfter(this.configField);

            // apply knockout binding
            dragulaVM = new DragulaElementViewModel(
                this.configField.val(),
                this.options.availableBlocks
            );

            ko.cleanNode(this.element.get(0));
            ko.applyBindings(dragulaVM, this.element.get(0));

            // initialize dragula
            this.drake = dragula(
                $('#' + this.options.parentId).find('.dragula-element').children().toArray(),
                {
                    moves: function (el, source, handle, sibling) {
                        return $(el).data('type') == 'block';
                    }
                }
            ).on('drop', dragulaVM.onDragulaDrop);

            // wrap toggle function to enable/disable dragula element
            toggleValueElements = wrapper.wrap(
                toggleValueElements,
                function (
                    callOriginal, checkbox, container, excludedElements, checked
                ) {
                    var result = callOriginal(
                        checkbox,
                        container,
                        excludedElements,
                        checked
                    );

                    $(container).find('.dragula-element').toggleClass('disabled');

                    return result;
                }
            );

            this.hideSpinner();
        },

        /**
         * Hide initial spinner on layout config
         */
        hideSpinner: function () {
            this.element.children('[data-role="spinner"]').hide();
        }
    });

    return $.swissup.headerLayout;
});
