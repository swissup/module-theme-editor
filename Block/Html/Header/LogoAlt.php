<?php

namespace Swissup\ThemeEditor\Block\Html\Header;

use Magento\Config\Model\Config\Backend\Image\Logo;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\ScopeInterface;

class LogoAlt extends \Magento\Theme\Block\Html\Header\Logo
{
    /**
     * @var string
     */
    protected $_template = 'Swissup_ThemeEditor::header/logo-alt.phtml';

    /**
     * @return string
     */
    protected function _getLogoUrl()
    {
        $logo = $this->getConfigValue('design/header/logo_alt_src');

        if ($logo) {
            $folder = Logo::UPLOAD_DIR;
        } elseif ($this->hasData('logo_src_fallback_config')) {
            // old argento installations
            $folder = $this->getData('logo_src_fallback_folder');
            $logo = $this->getConfigValue($this->getData('logo_src_fallback_config'));
            if (!$logo) {
                return '';
            }
        }

        $path = $folder . '/' . $logo;
        if ($this->_isFile($path)) {
            return $this->_urlBuilder
                ->getBaseUrl(['_type' => UrlInterface::URL_TYPE_MEDIA])
                . $path;
        }

        return '';
    }

    /**
     * @param  string $path
     * @param  string $scope
     * @return mixed
     */
    private function getConfigValue($path, $scope = ScopeInterface::SCOPE_STORE)
    {
        return $this->_scopeConfig->getValue($path, $scope);
    }
}
