define([
    'jquery',
    'mage/utils/wrapper',
    'Swissup_ThemeEditor/js/codemirror/lib/codemirror',
    'Swissup_ThemeEditor/js/codemirror/mode/css/css',
    'Swissup_ThemeEditor/js/codemirror/addon/hint/show-hint',
    'Swissup_ThemeEditor/js/codemirror/addon/hint/css-hint',
    'Swissup_ThemeEditor/js/codemirror/addon/edit/closebrackets',
], function ($, wrapper, codeMirror) {
    'use strict';

     return {
        configCssEditor: function (options, element) {
            var configElement = $(element).prev(["class*='admin__control'"]),
                parent = $(element).parent('.config-field-wrapper'),
                editor = codeMirror.fromTextArea(configElement[0], {
                    lineNumbers: true,
                    mode: 'css',
                    autoCloseBrackets: true,
                    extraKeys: {"Ctrl-Space": "autocomplete"},
                });

            if (options.disabled) {
                editor.setOption('readOnly', 'nocursor');
                parent.css('opacity', 0.5);
            }

            // wrap toggle function to enable/disable editor
            window.toggleValueElements = wrapper.wrap(
                window.toggleValueElements,
                function (
                    callOriginal, checkbox, container, excludedElements, checked
                ) {
                    var result = callOriginal(
                        checkbox,
                        container,
                        excludedElements,
                        checked
                    );

                    editor.setOption('readOnly', checkbox.checked ? 'nocursor' : false);
                    parent.css('opacity', checkbox.checked ? 0.5 : 1);
                    return result;
                }
            );
        }
     };
});
