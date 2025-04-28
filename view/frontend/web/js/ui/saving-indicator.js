define([
    'jquery'
], function ($) {
    'use strict';

    const add = (button) => {
        $(button).addClass('saving-indicator');
        $(button).removeClass('saving-complete');
        $(button).attr('disabled', true);
    };

    const complete = (button) => {
        $(button).removeClass('saving-indicator');
        $(button).addClass('saving-complete');
        $(button).removeAttr('disabled');
    };

    return {
        add: add,
        complete: complete
    };
});
