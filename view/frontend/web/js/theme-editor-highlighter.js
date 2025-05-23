define([
    'jquery',
    'domReady!'
], function ($) {
    'use strict';

    const addBorder = (element) => {
        // $(element).css('outline', '3px bold blue');
        $(element).addClass('theme-editor-selected-border');
        $(element).addClass('theme-editor-selected-mask');
    };
    const removeBorder = (element) => {
        // $(element).css('outline', '');
        $(element).removeClass('theme-editor-selected-border');
        $(element).removeClass('theme-editor-selected-mask');
    };

    const settingsElementClass = 'theme-editor-settings';

    const debounce = function (func, delay) {
        let timeoutId;
        return function(...args) {
            clearTimeout(timeoutId);
            timeoutId = setTimeout(() => {
                func.apply(this, args);
            }, delay);
        };
    };

    const onChangePropertyInput = debounce(function() {
        const propertySettings = $(this).closest('li').data('target-property-settings');
        const { selector, property, value } = propertySettings;
        $(`${selector}`).css(property, $(this).val());

        $(this).closest('li').find('i').text(`${property}:${value} => ${$(this).val()};`);
        // hasNotSaveChanges = true;
    }, 300);

    const renderProperties = function (properties) {
        const $listContainer = $('#theme-editor-current-settings-selector-properties');
        $listContainer.empty();

        if (!properties || properties.length === 0) {
            $listContainer.append('<li>No properties to display.</li>');
            return;
        }

        properties.forEach(propertySettings => {
            const $listItem = $('<li>');
            const { id, label, property, value } = propertySettings;
            const inputType = (property?.includes('color') || property?.includes('-bg'))
                && !value.includes('rgb') ? 'color' : 'text';
            $listItem.attr('data-target-property-settings', JSON.stringify(propertySettings));
            $listItem.html(`
                <label title="Id: ${id}">${label}</label>
                <i>${property}:${value};</i>
                <input name="value" type="${inputType}" value="${value}">
            `);
            $listContainer.append($listItem);
        });

        $('#theme-editor-current-settings-selector-properties li input').off().on('input', function(){
            onChangePropertyInput.call(this);
        });
    };

    const onChangeConfig = function (element) {
        const targetSettings = element.data('target-settings');
        renderProperties(targetSettings.properties);

        const targetSelector = element.data('target-selector');
        const targetConfigId = element.data('target-config-id');

        // $('#theme-editor-current-settings-config-id').text(targetConfigId);

        const $currentElement = $('#theme-editor-current-settings-selector');
        $currentElement.val(targetSelector)
            .attr('data-target-config-id', targetConfigId)
            .attr('data-target-selector', targetSelector);

        $('#theme-editor-choose-current').val(targetConfigId);

        $('#theme-editor-toolbar-current-element-container').show();
    };

    $(document).ready(function() {
        $(document).on('mouseenter', '.' + settingsElementClass, function() {
            const targetId = $(this).data('target-id');
            addBorder($(`[data-theme-editor-id="${targetId}"]`));
            // $(`[data-theme-editor-id="${targetId}"]`).css('outline', '3px bold blue');
            const targetSelector = $(this).data('target-selector');
            addBorder($(`${targetSelector}`));
            // $(`${targetSelector}`).css('outline', '3px solid blue');
        });

        $(document).on('mouseleave', '.' + settingsElementClass, function() {
            const targetId = $(this).data('target-id');
            removeBorder($(`[data-theme-editor-id="${targetId}"]`));
            // $(`[data-theme-editor-id="${targetId}"]`).css('outline', '');
            const targetSelector = $(this).data('target-selector');
            // $(`${targetSelector}`).css('outline', '');
            removeBorder($(`${targetSelector}`));
        });

        $(document).on('click', '.' + settingsElementClass, function() {
            onChangeConfig($(this));
        });
    });

    const component = function (config) {
        $.each(config, function (i, settings) {
            const { key, selector } = settings;
            const elements = $(selector);
            if (elements.length) {
                elements.each(function (index) {
                    const $this = $(this);
                    // const themeEditorData = {
                    //     name: key,
                    //     selector
                    // };
                    // $(this).attr('data-theme-editor', JSON.stringify(themeEditorData));
                    // $this.css('outline', `2px dashed ${borderColor}`);

                    const uniqueId = `theme-editor-target-${index}`;
                    $this.attr('data-theme-editor-id', uniqueId);

                    const $settingsElement = $('<span></span>')
                        .addClass(settingsElementClass)
                        .text('⚙️')
                        .attr('data-target-id', uniqueId)
                        .attr('data-target-settings', JSON.stringify(settings))
                        .attr('data-target-config-id', key)
                        .attr('data-target-selector', selector)
                        .attr('title', `<${key}> - $('${selector}')`)
                        .appendTo('body');

                    const updateSettingsPosition = () => {
                        const rect = $this[0].getBoundingClientRect();
                        $settingsElement.css({
                            'top': rect.top + window.scrollY -10,
                            'left': rect.right + window.scrollX + 1
                        });
                    };

                    updateSettingsPosition();
                    $(window).on('scroll resize', updateSettingsPosition);

                    $this.data('settingsElement', $settingsElement);
                });
            } else {
                // console.warn(`Selector "${key}" (${selector}) not found on the page.`);
            }
        });
    };
    component.component = 'Swissup_ThemeEditor/js/theme-editor-highlighter';

    component.onChangeConfig = onChangeConfig;
    component.border = {
        add: addBorder,
        remove: removeBorder
    };

    return component;
});
