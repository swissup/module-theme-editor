define([
    'jquery',
    'mage/utils/wrapper',
    'ace/ace',
    'ace/ext/language_tools'
], function ($, wrapper, ace) {
    'use strict';

     return {
        configCssEditor: function (options, element) {
            var configElement = $(element).prev(["class*='admin__control'"]);
            var parent = $(element).parent('.config-field-wrapper');
            configElement.hide();

            var editor = ace.edit(element);
            editor.setValue(configElement.val());
            editor.clearSelection();
            editor.setOptions({
                mode: 'ace/mode/css',
                maxLines: 30,
                minLines: 30,
                fontSize: 14,
                enableBasicAutocompletion: true
            });

            if (options.disabled) {
                editor.setReadOnly(true);
                parent.css('opacity', 0.5);
            }

            editor.on("change", function(e) {
                configElement.val(editor.getValue());
            });

            // wrap toggle function to enable/disable editor
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
                    editor.setReadOnly(checkbox.checked);
                    parent.css('opacity', checkbox.checked ? 0.5 : 1);
                    return result;
                }
            );
        }
     };
});
