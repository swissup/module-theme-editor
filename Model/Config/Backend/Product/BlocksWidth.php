<?php

namespace Swissup\ThemeEditor\Model\Config\Backend\Product;

use Swissup\ThemeEditor\Model\Config\Backend\PageLayout;

class BlocksWidth extends PageLayout
{
    /**
     * {@inheritdoc}
     */
    public function generateXml()
    {
        $width = (float)$this->getData('value');
        if ($width === 0 || $width >= 100) {
            $css = '.catalog-product-view .column.main{display:flex;flex-direction:column}' .
                '.catalog-product-view .product.media{order:-1}' .
                $this->getSelector() . '{float:none;width:auto}';
        } else {
            $css = $this->getSelector() . '{width:' . $width . '%}';
        }

        return '<body>' .
            '<referenceContainer name="after.body.start">' .
                "<block class=\"Magento\Framework\View\Element\Text\" name=\"{$this->getData('blockName')}\" after=\"-\">" .
                    '<arguments>' .
                        '<argument name="text" xsi:type="string">' .
                            "<![CDATA[<style>@media screen and (min-width: 768px) {{$css}}</style>]]>" .
                        '</argument>' .
                    '</arguments>' .
                '</block>' .
            '</referenceContainer>' .
        '</body>';
    }
}
