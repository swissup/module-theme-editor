define([
    'jquery'
], function ($) {
    'use strict';

    return function (options, element) {
        /**
         * [_updateTitle description]
         */
        function _updateTitle() {
            var currentTheme;

            currentTheme = $('._active', element).text();

            if (currentTheme) {
                $('.admin__page-nav-title', element).html(
                    options.title.selected.replace('{{themeName}}', currentTheme)
                );
            } else {
                $('.admin__page-nav-title', element).html(options.title.no);
            }
        }

        /**
         * Assign image to theme
         */
        function _assignImage() {
            var themeTitle,
                image,
                html = '<div class="img-wrapper"><img src="{{image}}" width="240" /></div>';

            themeTitle = $(this).text().trim();
            image = options.images[themeTitle];

            if (image) {
                $(this).prepend(html.replace('{{image}}', image));
            }
        }

        /**
         * [_activateItem description]
         */
        function _activateItem() {
            $(options.itemToActivateWhenSelected).closest('.config-nav-block').collapsible('activate');
            $(options.itemToActivateWhenSelected).addClass('_active');
        }

        /**
         * Update `Select Theme` collapsible status
         */
        function _updateCollapsibleStatus() {
            var currentTheme;

            if (typeof $(element).collapsible === 'function') {
                currentTheme = $('._active', element).text();

                $('.admin__page-nav-items', element).css({
                    display: 'flex'
                });

                if (currentTheme) {
                    $(element).collapsible('deactivate');
                    $('.admin__page-nav-items', element).css({
                        display: 'none'
                    });
                    _activateItem();
                } else {
                    $(element).collapsible('activate');
                }
            } else {
                setTimeout(_updateCollapsibleStatus, 400);
            }
        }

        $(element).detach();
        $('a', element).each(_assignImage);
        _updateTitle();
        _updateCollapsibleStatus();
        $(element).appendTo(options.destination);
    };
});
