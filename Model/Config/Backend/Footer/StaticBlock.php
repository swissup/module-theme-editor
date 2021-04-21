<?php

namespace Swissup\ThemeEditor\Model\Config\Backend\Footer;

class StaticBlock extends \Swissup\ThemeEditor\Model\Config\Backend\PageLayout
{
    /**
     * {@inheritdoc}
     */
    public function generateXml()
    {
        $value = $this->getData('value');
        if ($value) {
            return '<body>' .
                '<referenceContainer name="footer-toolbar-bottom">' .
                    '<block class="Magento\Cms\Block\Block" name="footer_toolbar_bottom_cms_links">' .
                        '<arguments>' .
                            '<argument name="block_id" xsi:type="string">' . $value . '</argument>' .
                        '</arguments>' .
                    '</block>' .
                '</referenceContainer>' .
            '</body>';
        }

        return '';
    }
}
