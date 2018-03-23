<?php
namespace Swissup\ThemeEditor\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

class Helper extends AbstractHelper
{
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
}
