<?php
namespace Swissup\ThemeEditor\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

class Helper extends AbstractHelper
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\View\Design\Theme\ThemeProviderInterface
     */
    protected $themeProvider;

    /**
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\View\Design\Theme\ThemeProviderInterface $themeProvider
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\View\Design\Theme\ThemeProviderInterface $themeProvider,
        \Magento\Framework\App\Helper\Context $context
    ) {
        $this->themeProvider = $themeProvider;
        $this->storeManager = $storeManager;
        return parent::__construct($context);
    }

    /**
     * Convert capital letters to hyphens in string
     * Example:
     * in:  body_backgroundColor
     * out: body_background-color
     * @param  String camel case string
     * @return String string with hyphens
     */
    public function camel2dashed($str) {
        return strtolower(preg_replace('/([a-zA-Z])(?=[A-Z])/', '$1-', $str));
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
     * Get current theme
     *
     * @return \Magento\Theme\Model\Theme
     */
    public function getCurrentTheme()
    {
        $themeId = $this->scopeConfig->getValue(
            \Magento\Framework\View\DesignInterface::XML_PATH_THEME_ID,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $this->storeManager->getStore()->getId()
        );
        return $this->themeProvider->getThemeById($themeId);
    }
}
