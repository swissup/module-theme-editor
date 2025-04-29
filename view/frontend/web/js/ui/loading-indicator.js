define([
    'jquery'
], function ($) {
    'use strict';

    const add = (element) => {
        $(element).addClass('loading-indicator');
        $(element).attr('disabled', true);
        // Можна додати додаткову візуалізацію завантаження, наприклад, спінер
    };

    const remove = (element) => {
        $(element).removeClass('loading-indicator');
        $(element).removeAttr('disabled');
        // Видалити додаткову візуалізацію завантаження, якщо вона була додана
    };

    return {
        add: add,
        remove: remove
    };
});
