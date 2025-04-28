define([], function () {
    'use strict';

    return function (themeCode, accessToken) {
            return `{
    getThemeEditorProperties(
        themeCode: "${themeCode}",
        accessToken: "${accessToken}"
    ) {
        key
        selector
        label
        groupLabel
        properties {
            id
            label
            path
            selector
            property
            value
        }
    }
}`;
    }
});
