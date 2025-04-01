<?php

namespace Swissup\ThemeEditor\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Area;
use Magento\Framework\View\Design\ThemeInterface;
use Magento\Store\Model\ScopeInterface;

class Header extends AbstractHelper
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
     * @var \Magento\Framework\View\LayoutInterface
     */
    private $layout;

    /**
     * @var Data
     */
    private $helper;

    /**
     * @param Context $context
     * @param Data $helper
     * @param \Magento\Framework\View\LayoutInterface $layout
     */
    public function __construct(
        Context $context,
        Data $helper,
        \Magento\Framework\View\LayoutInterface $layout
    ) {
        $this->helper = $helper;
        $this->layout = $layout;
        parent::__construct($context);
    }

    /**
     * Check if configurable header enabled
     *
     * @param  ThemeInterface $theme
     * @return boolean
     */
    public function isHeaderEnabled(?ThemeInterface $theme = null)
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
        $themeConfig = $this->helper->themeCodeToConfigPath($theme);

        $action = $this->_getRequest()->getFullActionName();
        $exclude = explode(
            "\n",
            $this->helper->getConfigValue($themeConfig . self::PATH_HEADER_EXCLUDE) ?: ''
        );
        $exclude = array_map('trim', $exclude);

        if (in_array($action, $exclude)) {
            return false;
        }

        return $this->helper->getConfigValue($themeConfig . self::PATH_HEADER_ENABLED);
    }

    /**
     * Get header layout config
     *
     * @param  ThemeInterface $theme
     * @return array
     */
    public function getHeaderLayout(?ThemeInterface $theme = null)
    {
        $scope = ScopeInterface::SCOPE_STORE;
        $scopeCode = null;
        if ($theme) {
            $section = $this->helper->themeCodeToConfigPath($theme);
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
            $section = $this->helper->themeCodeToConfigPath($theme);
        }

        if ($this->_getRequest()->getFullActionName() == self::PREVIEW_ACTION) {
            $configPath = $section . self::PATH_HEADER_LAYOUT_PREVIEW;
        } else {
            $configPath = $section . self::PATH_HEADER_LAYOUT;
        }
        $config = $this->helper->getConfigValue($configPath, $scope, $scopeCode);

        return $config ? json_decode($config, true) : [];
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
        $section = $this->helper->themeCodeToConfigPath($theme);
        $hashConfig = $this->helper->getConfigValue($section . self::PATH_HEADER_PREVIEW_HASH);

        if (isset($hashConfig) && $hash == $hashConfig) {
            $expire = $this->helper->getConfigValue($section . self::PATH_HEADER_PREVIEW_EXPIRES);

            if (isset($expire)) {
                return $expire >= time();
            }
        }

        return false;
    }
}
