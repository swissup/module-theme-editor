<?php

namespace Swissup\ThemeEditor\Model\Config\Backend\Product;

use Swissup\ThemeEditor\Model\Config\Backend\PageLayout;

class StickyAddToCart extends PageLayout
{
    /**
     * @var array
     */
    protected $mapping = [];

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->mapping = [
            'yes' => '<referenceBlock name="argento.sticky.add.to.cart">' .
                    '<arguments>' .
                        '<argument name="options" xsi:type="array">' .
                            '<item name="enabled" xsi:type="boolean">true</item>' .
                        '</argument>' .
                    '</arguments>'.
                '</referenceBlock>',
            'no' => '<referenceBlock name="argento.sticky.add.to.cart" remove="true" />'
        ];

        parent::_construct();
    }

    /**
     * {@inheritdoc}
     */
    public function generateXml()
    {
        $value = $this->getData('value');

        return $this->mapping[$value] ?? '';
    }
}
