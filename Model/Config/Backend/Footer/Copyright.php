<?php

namespace Swissup\ThemeEditor\Model\Config\Backend\Footer;

class Copyright extends \Swissup\ThemeEditor\Model\Config\Backend\PageLayout
{
    /**
     * {@inheritdoc}
     */
    public function generateXml()
    {
        $value = $this->getData('value');
        if ($value) {
            return '<body><referenceBlock name="copyright" remove="true" /></body>';
        }

        return '';
    }
}
