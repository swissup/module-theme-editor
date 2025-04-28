define([], function () {
    'use strict';

    return function (accessToken, propertyValues) {
        const props = propertyValues.map(item => {
            return `{ property: "${item.path}", value: "${item.value}" }`;
        }).join(',\n        ');

        return `mutation {
      updateThemeEditorPropertyValues(
        input: {
          accessToken: "${accessToken}",
          props: [
            ${props}
          ]
        }
      ) {
        success
        message
      }
    }`;
    }
});
