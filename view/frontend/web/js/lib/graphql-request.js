define([
    'jquery',
    'Swissup_ThemeEditor/js/lib/graphqlize'
], function ($, graphqlize) {
    'use strict';

    const request = (payload) => {
        const storeViewCode = payload.storeViewCode || 'default';
        const graphqlUrl = payload.graphqlUrl || '/graphql';
        let params = $.extend({}, {
            url: graphqlUrl,
            query: payload.query,
            headers: {
                Store: storeViewCode
            },
        });
        params = graphqlize(params);
        const $request = $.ajax(params);
        return $request; // Повертаємо без .promise()
    };

    return request;
});
