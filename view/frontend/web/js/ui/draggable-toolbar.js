define([
    'jquery',
    'domReady!'
], function ($) {
    'use strict';

    $(document).ready(function () {
        const toolbar = $('#theme-editor-toolbar');
        let isDragging = false;
        let offsetX, offsetY;

        toolbar.on('mousedown', function (event) {
            isDragging = true;
            offsetX = event.clientX - toolbar.offset().left;
            offsetY = event.clientY - toolbar.offset().top;
            toolbar.css('cursor', 'grabbing');
        });

        $(document).on('mousemove', function (event) {
            if (!isDragging) {
                return;
            }

            const newX = event.clientX - offsetX;
            const newY = event.clientY - offsetY;

            toolbar.css({
                left: newX + 'px',
                top: newY + 'px',
                position: 'fixed',
                'z-index': 1000
            });
        });

        $(document).on('mouseup', function () {
            if (isDragging) {
                isDragging = false;
                toolbar.css('cursor', 'grab');
            }
        });

        toolbar.css('cursor', 'grab');
    });
});
