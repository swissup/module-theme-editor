/* global configForm */
define([
    'jquery',
    'Magento_Ui/js/modal/alert'
], function ($, alert) {
    'use strict';

    $.widget('swissup.confHeaderPreview', {
        options: {
            target: '',
            source: '',
            previewUrl: ''
        },

        /**
         * {@inheritdoc}
         */
        _create: function () {
            this.target = $(this.options.target);
            this.source = $(this.options.source);
            this._on({
                'click': this.saveAndPreview
            });
        },

        /**
         * Save config and start preview of configurable header
         */
        saveAndPreview: function () {
            if (configForm.validation('isValid')) {
                $('#theme_editor_header__preview').prop('disabled', true);
                // save config
                $.ajax({
                    method: 'POST',
                    url: configForm.attr('action'),
                    data: {
                        form_key: window.FORM_KEY,
                        'groups[header_config][fields][preview_hash][value]': jQuery('input[id$="header_config_preview_hash"').val(),
                        'groups[header_config][fields][preview_expires][value]': jQuery('input[id$="header_config_preview_expires"').val(),
                        'groups[header_config][fields][preview][value]': jQuery('textarea[id$="header_config_layout"').val()
                    },
                    context: this
                }).done(function (response) {
                    if (typeof response == 'object') {
                        this.handleJsonResponse(response);
                    } else {
                        this.handleHtmlResponse(response);
                    }

                    $('#theme_editor_header__preview').prop('disabled', false);
                    this.spinnerHide();
                });

                this.target.find('iframe').remove();
                this.spinnerShow();
            } else {
                // todo: config form is invalid
            }
        },

        /**
         * Show spinner over loading iframe
         */
        spinnerShow: function () {
            this.target.find('[data-role="iframe-placeholder"]')
                .show()
                .css('position', '');
        },

        /**
         * Hide spinner and scroll to iframe
         */
        spinnerHide: function () {
            $('html, body').animate({
                scrollTop: this.target.offset().top - 120
            }, 600);
            this.target.find('[data-role="iframe-placeholder"]')
                .css('position', 'absolute')
                .delay(6000)
                .hide(0); // pass 0 so delay could work on hide
        },

        /**
         * Get new hash and expire values from returned HTML
         *
         * @param  {HTML} html
         */
        getNewHashAndExpire: function (html) {
            var $html = $(html),
                rowId = this.source.parent().attr('id'),
                hashSelector = '#' + rowId + '_hash .value input',
                expiresSelector = '#' + rowId + '_expires .value input';

            $(hashSelector).val($html.find(hashSelector).val());
            $(expiresSelector).val($html.find(expiresSelector).val());
        },

        /**
         * Handle HTML response received from server
         *
         * @param  {HTML} html
         */
        handleHtmlResponse: function (html) {
            // start preview on successful save
            var iframe;

            iframe = $('<iframe></iframe>', {
                src: this.options.previewUrl.replace(
                        '{{previewHash}}',
                        $('input[id$="header_config_preview_hash"').val()
                    )
            }).css({
                'width': '100%',
                'border': '1px solid #d6d6d6',
                'min-height': '300px',
                'min-width': '380px',
                'max-width': '1200px',
                'resize': 'both'
            });
            this.target.append(iframe);
            this.getNewHashAndExpire(html);
        },

        /**
         * Handle JSON response recived from server
         *
         * @param  {JSON} json
         */
        handleJsonResponse: function (json) {
            if (json.error) {
                alert({
                    title: 'Error', content: json.message
                });
            } else if (json.ajaxExpired) {
                window.location.href = json.ajaxRedirect;
            }
        }
    });

    return $.swissup.confHeaderPreview;
});
