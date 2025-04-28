define([
    'jquery',
    'Magento_Customer/js/customer-data'
], function ($, customerData) {
    'use strict';

    const show = (message) => {
        customerData.set('messages', {
            messages: [{
                text: message,
                type: 'error'
            }]
        });
    };

    return {
        show: show
    };
});
