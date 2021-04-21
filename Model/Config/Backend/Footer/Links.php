<?php

namespace Swissup\ThemeEditor\Model\Config\Backend\Footer;

class Links extends \Swissup\ThemeEditor\Model\Config\Backend\PageLayout
{
    /**
     * {@inheritdoc}
     */
    public function generateXml()
    {
        $value = $this->getData('value');
        if ($value) {
            return '<body><referenceBlock name="footer_links" remove="true" /></body>';
        }

        return '';
    }
}
