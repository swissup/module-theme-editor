<?php

namespace Swissup\ThemeEditor\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\View\Design\ThemeInterface;
use Magento\Framework\App\Area;

class Data extends AbstractHelper
{
    /**
     * Configurable header preview action name
     */
    const PREVIEW_ACTION = 'swissupeditor_header_preview';

    /**
     * Configurable header enabled
     */
    const PATH_HEADER_ENABLED = '/header_config/enabled';

    /**
     * Configurable header layout
     */
    const PATH_HEADER_LAYOUT = '/header_config/layout';

    /**
     * Configurable header preview hash
     */
    const PATH_HEADER_PREVIEW_HASH = '/header_config/preview_hash';

    /**
     * Configurable header preview expires
     */
    const PATH_HEADER_PREVIEW_EXPIRES = '/header_config/preview_expires';

    /**
     * Configurable header preview layout
     */
    const PATH_HEADER_LAYOUT_PREVIEW = '/header_config/preview';

    /**
     * Configurable header disable on pages
     */
    const PATH_HEADER_EXCLUDE = '/header_config/exclude';

    /**
     * @var \Magento\Framework\View\ConfigInterface
     */
    private $viewConfig;

    /**
     * @var \Magento\Framework\Config\View
     */
    private $configView;

    /**
     * @var \Magento\Framework\View\LayoutInterface
     */
    private $layout;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\View\ConfigInterface $viewConfig,
        \Magento\Framework\View\LayoutInterface $layout
    ) {
        $this->viewConfig = $viewConfig;
        $this->layout = $layout;
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

    /**
     * Check if configurable header enabled
     *
     * @param  ThemeInterface $theme
     * @return boolean
     */
    public function isHeaderEnabled(ThemeInterface $theme = null)
    {
        if (in_array(
            'swissupeditor_header_preview',
            $this->layout->getUpdate()->getHandles())
        ) {
            return true;
        }

        if (!$theme) {
            $theme = $this->layout->getUpdate()->getTheme();
        }
        $themeConfig = $this->themeCodeToConfigPath($theme->getCode());

        $action = $this->_getRequest()->getFullActionName();
        $exclude = explode("\n", $this->getConfigValue($themeConfig . self::PATH_HEADER_EXCLUDE));
        if (in_array($action, $exclude)) {
            return false;
        }

        return $this->getConfigValue($themeConfig . self::PATH_HEADER_ENABLED);
    }

    /**
     * Get header layout config
     *
     * @param  ThemeInterface $theme
     * @return array
     */
    public function getHeaderLayout(ThemeInterface $theme = null)
    {
        $scope = ScopeInterface::SCOPE_STORE;
        $scopeCode = null;
        if ($theme) {
            $section = $this->themeCodeToConfigPath($theme->getCode());
        } else if ($this->_getRequest()->getRouteName() == Area::AREA_ADMINHTML) {
            $section = $this->_getRequest()->getParam('section');

            if ($this->_getRequest()->getParam('store') !== null) {
                $scopeCode = $this->_getRequest()->getParam('store');
            }

            if ($this->_getRequest()->getParam('website') !== null) {
                $scope = ScopeInterface::SCOPE_WEBSITE;
                $scopeCode = $this->_getRequest()->getParam('website');
            }
        } else {
            $theme = $this->layout->getUpdate()->getTheme();
            $section = $this->themeCodeToConfigPath($theme->getCode());
        }

        if ($this->_getRequest()->getFullActionName() == self::PREVIEW_ACTION) {
            $configPath = $section . self::PATH_HEADER_LAYOUT_PREVIEW;
        } else {
            $configPath = $section . self::PATH_HEADER_LAYOUT;
        }
        $config = $this->getConfigValue($configPath, $scope, $scopeCode);

        return $config ? json_decode($config, true) : [];
    }

    /**
     * Convert theme code to config path
     *
     * @param  string $code
     * @return string
     */
    public function themeCodeToConfigPath($code)
    {
        // manually selected theme editor
        $editor = $this->getThemeEditorCode();
        if ($editor) {
            return $editor;
        }

        return strtolower(str_replace(['/', '-'], '_', $code));
    }

    /**
     * @return string|null
     */
    public function getThemeEditorCode()
    {
        return $this->getConfigValue('design/swissup_theme_editor/code');
    }

    /**
     * Check if preview can be displayed for current theme
     *
     * @param  String $hash
     * @return bool
     */
    public function validatePreviewHash($hash)
    {
        $theme = $this->layout->getUpdate()->getTheme();
        $section = $this->themeCodeToConfigPath($theme->getCode());
        $hashConfig = $this->getConfigValue($section . self::PATH_HEADER_PREVIEW_HASH);

        if (isset($hashConfig) && $hash == $hashConfig) {
            $expire = $this->getConfigValue($section . self::PATH_HEADER_PREVIEW_EXPIRES);

            if (isset($expire)) {
                return $expire >= time();
            }
        }

        return false;
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
