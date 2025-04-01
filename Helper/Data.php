<?php

namespace Swissup\ThemeEditor\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\View\Design\ThemeInterface;
use Magento\Store\Model\ScopeInterface;

class Data extends AbstractHelper
{
    /**
     * @var \Magento\Framework\View\ConfigInterface
     */
    private $viewConfig;

    /**
     * @var \Magento\Framework\Config\View
     */
    private $configView;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\View\ConfigInterface $viewConfig
    ) {
        $this->viewConfig = $viewConfig;
        parent::__construct($context);
    }

    /**
     * Get scope config
     * @return \Magento\Framework\App\Config\ScopeConfigInterface
     */
    public function getScopeConfig()
    {
        return $this->scopeConfig;
    }

    /**
     * Get specific config value
     *
     * @param  string $path
     * @param  string $scope
     * @param  null|string $scopeCode
     * @return mixed
     */
    public function getConfigValue($path, $scope = ScopeInterface::SCOPE_STORE, $scopeCode = null)
    {
        return $this->scopeConfig->getValue($path, $scope, $scopeCode);
    }

    /**
     * @return \Magento\Framework\Config\View
     */
    public function getViewConfig()
    {
        if (!$this->configView) {
            $this->configView = $this->viewConfig->getViewConfig();
        }
        return $this->configView;
    }

    /**
     * Get specific config value from view.xml file
     *
     * @param  string $path
     * @param  string $module
     * @return mixed
     */
    public function getViewConfigValue($path, $module = 'Swissup_ThemeEditor')
    {
        return $this->getViewConfig()->getVarValue($module, $path);
    }

    public function getLessStyles(ThemeInterface $theme): string
    {
        return (string) $this->getConfigValue(
            $this->themeCodeToConfigPath($theme) . '/head/less'
        );
    }

    /**
     * Convert theme code to config path
     *
     * @param  string $code
     * @return string
     */
    public function themeCodeToConfigPath(ThemeInterface $theme)
    {
        // manually selected theme editor
        $editor = $this->getThemeEditorCode($theme);
        if ($editor) {
            return $editor;
        }

        return strtolower(str_replace(['/', '-'], '_', $theme->getCode()));
    }

    /**
     * @return string|null
     */
    public function getThemeEditorCode(?ThemeInterface $theme = null)
    {
        if ($theme) {
            $viewConfig = $this->viewConfig->getViewConfig(['themeModel' => $theme]);
        } else {
            $viewConfig = $this->getViewConfig();
        }

        try {
            $code = $viewConfig->getVarValue('Swissup_ThemeEditor', 'code');
        } catch (\InvalidArgumentException $e) {
            /**
             * Catch exception when select widget container in Magento Admin.
             *
             * Required parameter 'theme_dir' was not passed in
             * magento/framework/View/Design/Fallback/Rule/Simple.php
             */
            $code = null;
        }

        // fallback to deprecated config value
        if (!$code) {
            $code = $this->getConfigValue('design/swissup_theme_editor/code');
        }

        return $code;
    }

    /**
     * Convert capital letters to hyphens in string
     * Example:
     * in:  body_backgroundColor
     * out: body_background-color
     * @param  String camel case string
     * @return String string with hyphens
     */
    public function camel2dashed($str)
    {
        return strtolower(preg_replace('/([a-zA-Z])(?=[A-Z])/', '$1-', $str));
    }
}
