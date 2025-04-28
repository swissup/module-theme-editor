define([
    'jquery',
    'Swissup_ThemeEditor/js/theme-editor-highlighter'
], function ($, highlighter) {
    'use strict';

    const init = ($select, properties) => {
        if (!$select.length) {
            return;
        }
        $select.empty();
        $select.append($('<option>', {
            value: '',
            text: 'Select configurable element(s)',
            selected: 'selected',
            disabled: 'disabled'
        }));

        const groupedData = {};
        for (const i in properties) {
            if (properties.hasOwnProperty(i)) {
                const groupLabel = properties[i]?.groupLabel || 'Ungrouped';
                if (!groupedData[groupLabel]) {
                    groupedData[groupLabel] = [];
                }
                groupedData[groupLabel].push(i);
            }
        }

        for (const groupLabel in groupedData) {
            if (groupedData.hasOwnProperty(groupLabel)) {
                const $optgroup = $('<optgroup>', {
                    label: groupLabel
                });

                groupedData[groupLabel].forEach(i => {
                    const property = properties[i];
                    const { key, label, selector } = property;
                    $optgroup.append($('<option>', {
                        value: key,
                        text: key, //`${label}  ${key}`,
                        'data-target-settings': JSON.stringify(property),
                        'data-target-selector': selector,
                        'data-target-config-id': key
                    }));
                });

                $select.append($optgroup);
            }
        }

        if ($select.find('option').length === 1) {
            $select.hide();
            return;
        }

        $select.off('change').on('change', function() {
            const $selectedOption = $(this).find(':selected');
            const targetSettings = $selectedOption.data('target-settings');
            const targetSelector = $selectedOption.data('target-selector');
            const targetConfigId = $selectedOption.data('target-config-id');

            if (typeof highlighter.onChangeConfig === 'function') {
                require(['Swissup_ThemeEditor/js/theme-editor-highlighter'], function (highlighter) {
                    highlighter.onChangeConfig($selectedOption);
                });
            }
        });
    };

    return {
        init: init
    };
});
