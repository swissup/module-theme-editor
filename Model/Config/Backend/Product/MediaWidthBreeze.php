<?php

namespace Swissup\ThemeEditor\Model\Config\Backend\Product;

use Swissup\ThemeEditor\Model\Config\Backend\PageLayout;

class MediaWidthBreeze extends PageLayout
{
    public function generateXml()
    {
        $width = (float)$this->getData('value');
        $css = $this->getSelector() . '{ grid-template-columns:' . $width . '% 1fr; }';

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
