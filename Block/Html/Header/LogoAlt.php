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
            $folder = Logo::UPLOAD_DIR . '/';
        } else {
            $logo = $this->getVar('logo_alt');
            $folder = '';
        }

        if (is_array($logo)) {
            if (!isset($logo['config'])) {
                return '';
            }

            if (isset($logo['depends']) && is_array($logo['depends'])) {
                $value = $this->getConfigValue($logo['depends']['config']);
                if ($value != $logo['depends']['value']) {
                    return '';
                }
            }

            $folder = explode('/', $logo['config']);
            $folder = str_replace('_', '/', $folder[0]) . '/images/';
            $logo = $this->getConfigValue($logo['config']);
        }

        if (!$logo) {
            return '';
        }

        $path = $folder . $logo;
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
