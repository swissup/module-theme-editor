/* eslint-disable no-native-reassign */
define([
    'jquery',
    'underscore',
    'mage/template',
    'text!Swissup_ThemeEditor/template/layout.html',
    'jquery/ui',
    'Magento_Ui/js/modal/modal' // 2.3.3: create 'jquery-ui-modules/widget' dependency
], function ($, _, mageTemplate, tmplLayout) {
    'use strict';

    /**
     * Listen click on inherit checkbox to enable/disbale layout.
     */
    $('body').on('change', '.config-inherit', (event) => {
        const $layout = $(event.currentTarget)
            .closest('.use-default')
            .siblings('.value')
            .find('.header-layout');

        $layout.toggleClass('disabled');
        $layout.trigger('swissup::toggleIsInherit');
    });

    $.widget('swissup.themeEditorHeaderBuilder', {
        containers: [],

        allowedBlocks: {},

        isInherit: false,

        $layout: false,

        /**
         * @private
         */
        _create: function () {
            const me = this,
                $el = me.element;

            me.containers = me._readContainers();
            me.allowedBlocks = me._readAllowedBlocks();
            me.isInherit = me.options.isInherit;

            $el.find('textarea').hide();
            me.updateLayout();
            $el.find('[data-role="spinner"]').hide();

            me._on({
                'click .settings-action': me.showConfig.bind(me),
                'input': _.debounce(me.updateContainers.bind(me), 200),
                'swissup::toggleIsInherit': (event) => {
                    me.isInherit = !me.isInherit;
                }
            });

            me._initAllowedContainer();
        },

        _initAllowedContainer: function () {
            $(this.options.allowedContainer).sortable({
                connectWith: [this.options.allowedContainer, this.options.layoutContainers].join(','),
                handle: '.name',
                start: (event, ui) => {
                    ui.placeholder.html(ui.item.html());
                },
                tolerance: "pointer"
            });
        },

        /**
         * @private
         */
        _readAllowedBlocks: function () {
            return this.options.availableBlocks;
        },

        /**
         * @private
         */
        _readContainers: function () {
            return JSON.parse(this.element.find('textarea').val());
        },

        /**
         * @private
         */
        _renderLayout: function () {
            const $layout = $(mageTemplate(tmplLayout, this));

            $layout.appendTo(this.element);

            return $layout;
        },

        updateLayout: function () {
            const me = this;

            me.$layout && me.$layout.remove();
            me.$layout = me._renderLayout();
            $(me.options.layoutContainers).sortable({
                connectWith: [me.options.allowedContainer, me.options.layoutContainers].join(','),
                handle: '.name',
                start: (event, ui) => {
                    ui.placeholder.html(ui.item.html());
                },
                tolerance: "pointer",
                update: () => {
                    me.updateContainers();
                    me.updateLayout();
                }
            });
        },

        updateContainers: function () {
            const me = this,
                $el = me.element;

            var containers = [];
            $(me.options.layoutContainers).each(function () {
                var children = [],
                    config = {};

                $(this).find('[data-type="block"]').each(function () {
                    children.push({name: $(this).data('name')});
                });

                $(this).find('.settings-dropdown :input').serializeArray().each(item => {
                    config[item.name.split('.').pop()] = item.value;
                });

                containers.push({
                    children: children,
                    config: config,
                    name: $(this).data('name')
                });
            });

            $el.find('textarea').val(JSON.stringify(containers));
            me.containers = me._readContainers();
        },

        showConfig: function (event) {
            const curItem = $(event.currentTarget).closest('[data-type="container"]').find('.settings-dropdown'),
                curState = curItem.hasClass('shown');

            $('[data-type="container"] .settings-dropdown').removeClass('shown');
            if (!curState) {
                curItem.addClass('shown');
            }

            event.preventDefault();
        }
    });

    return $.swissup.themeEditorHeaderBuilder;
});
