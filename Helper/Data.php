<?php

namespace Swissup\ThemeEditor\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
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
     * @return mixed
     */
    public function getConfigValue($path, $scope = ScopeInterface::SCOPE_STORE)
    {
        return $this->scopeConfig->getValue($path, $scope);
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
