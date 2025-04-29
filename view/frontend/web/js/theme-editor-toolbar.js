define([
    'jquery',
    'Swissup_ThemeEditor/js/theme-editor-highlighter',
    'Swissup_ThemeEditor/js/lib/graphql-request',
    'Swissup_ThemeEditor/js/get-graphql-query',
    'Swissup_ThemeEditor/js/get-graphql-mutation',
    'Swissup_ThemeEditor/js/ui/error-message',
    'Swissup_ThemeEditor/js/ui/loading-indicator',
    'Swissup_ThemeEditor/js/ui/saving-indicator',
    'Swissup_ThemeEditor/js/ui/property-chooser',
    'Swissup_ThemeEditor/js/ui/draggable-toolbar',
    'domReady!'
], function ($, highlighter, graphqlRequest, getGraphQlQuery, getGraphQlMutation, errorMessage, loadingIndicator, savingIndicator, propertyChooser) {
    'use strict';

    let configSettings;
    const TOOLBAR_ACTIVE_STORAGE_KEY = 'themeEditorToolbarActive';
    let propertiesLoaded = false;

    const handleMouseEnter = function() {
        const targetId = $(this).data('target-id');
        highlighter.border.add($(`[data-theme-editor-id="${targetId}"]`));
        const targetSelector = $(this).data('target-selector');
        highlighter.border.add($(`${targetSelector}`));
    };

    const handleMouseLeave = function() {
        const targetId = $(this).data('target-id');
        highlighter.border.remove($(`[data-theme-editor-id="${targetId}"]`));
        const targetSelector = $(this).data('target-selector');
        highlighter.border.remove($(`${targetSelector}`));
    };

    const handleClickSelector = function() {
        const targetSelector = $(this).data('target-selector');
        if (targetSelector.length && $(`${targetSelector}`).length > 0) {
            $(window).scrollTop($(`${targetSelector}`).offset().top);
        }
    };

    const handleSaveClick = function() {
        const button = $(this);
        savingIndicator.add(button);
        const propertyValues = [];
        $('#theme-editor-current-settings-selector-properties li').each(function () {
            const $li = $(this);
            const propertySettings = $li.data('target-property-settings');
            const { path, value } = propertySettings;
            const newValue = $li.find('input').val();
            if (newValue !== undefined) {
                propertyValues.push({ path, value: newValue });
            }
        });

        if (propertyValues.length === 0) {
            savingIndicator.complete(button);
            return;
        }

        const storeViewCode = configSettings.storeViewCode || 'default';
        const { accessToken } = configSettings;
        const mutation = getGraphQlMutation(accessToken, propertyValues);

        graphqlRequest({ query: mutation, storeViewCode })
            .then(response => {
                savingIndicator.complete(button);

                const result = response?.body?.data?.updateThemeEditorPropertyValues
                    || response?.data?.updateThemeEditorPropertyValues;
                const errors = response?.body?.errors || response?.errors;
                if (result) {
                    if (result.success) {
                        console.log('Конфігурацію успішно оновлено:', result.message);
                    } else {
                        console.error('Помилка оновлення конфігурації:', result.message);
                        errorMessage.show(result.message);
                    }
                } else if (errors) {
                    console.error('Помилка GraphQL:', errors);
                    errors.forEach(error => errorMessage.show(error.message));
                } else {
                    console.error('Неочікувана відповідь:', response);
                    if (response.text) {
                        try {
                            const responseText = JSON.parse(response.text);
                            if (responseText.errors && Array.isArray(responseText.errors) && responseText.errors.length > 0) {
                                responseText.errors.forEach(error => errorMessage.show(error.message));
                            }
                        } catch (e) {
                            console.error('Помилка парсингу JSON:', response.text, e);
                            errorMessage.show('Неочікувана помилка сервера.');
                        }
                    }
                }
            })
            .catch(error => {
                console.error('Помилка запиту:', error);
                errorMessage.show('Помилка під час збереження.');
                savingIndicator.complete(button);
            });
    };

    const loadProperties = () => {
        if (propertiesLoaded) {
            return;
        }

        const toolbarContent = $('#theme-editor-toolbar div.toolbar-content');
        loadingIndicator.add(toolbarContent);
        propertiesLoaded = false;

        const storeViewCode = configSettings.storeViewCode || 'default';
        const { configSectionName, accessToken } = configSettings;
        const query = getGraphQlQuery(configSectionName, accessToken);

        return graphqlRequest({ query, storeViewCode })
            .then(response => {
                const properties = response?.body?.data?.getThemeEditorProperties
                    || response?.data?.getThemeEditorProperties;
                highlighter(properties);
                propertyChooser.init($('#theme-editor-choose-current'), properties);
                propertiesLoaded = true;
                loadingIndicator.remove(toolbarContent);
            })
            .catch(error => {
                console.error('Помилка завантаження властивостей:', error);
                errorMessage.show('Не вдалося завантажити властивості.');
                loadingIndicator.remove(toolbarContent);
            });
    };

    const initToggler = async () => {
        const toggler = $('#theme-editor-highligh-toggler');
        const savedState = localStorage.getItem(TOOLBAR_ACTIVE_STORAGE_KEY);

        if (savedState === 'active') {
            toggler.addClass('active');
            $('#theme-editor-toolbar .toolbar-content').show();
            await loadProperties();
            $('.theme-editor-settings').show();
        } else {
            $('.theme-editor-settings').hide();
            $('#theme-editor-toolbar .toolbar-content').hide();
        }

        toggler.click(async function () {
            $(this).toggleClass('active');
            const isVisible = $(this).hasClass('active');
            $('#theme-editor-toolbar .toolbar-content').toggle(isVisible);
            localStorage.setItem(TOOLBAR_ACTIVE_STORAGE_KEY, isVisible ? 'active' : 'inactive');
            if (isVisible && !propertiesLoaded) {
                await loadProperties();
            }
            $('.theme-editor-settings').toggle(isVisible);
        });
    };

    const component = function (config) {
        configSettings = config;
        initToggler();

        $(document).ready(function() {
            $(document).on('mouseenter', '#theme-editor-current-settings-selector', handleMouseEnter);
            $(document).on('mouseleave', '#theme-editor-current-settings-selector', handleMouseLeave);
            $('#theme-editor-current-settings-selector').on('click', handleClickSelector);
            $('#theme-editor-highligh-save').on('click', handleSaveClick);
        });
    };
    component.component = 'Swissup_ThemeEditor/js/theme-editor-toolbar';

    return component;
});
