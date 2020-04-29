<?php

namespace Swissup\ThemeEditor\Model\Config\Backend\Product;

use Swissup\ThemeEditor\Model\Config\Backend\PageLayout;

class MoveTocart extends PageLayout
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
            // move tocart form into main section
            'product.info.main' => '<move element="product.info" destination="product.info.main" after="alert.urls" />' .
                '<referenceBlock name="move.image.under.title.on.mobile" remove="true" />',
            // Move tocart form into media section
            'product.info.media' => '<move element="product.info" destination="product.info.media" after="-" />' .
                '<referenceContainer name="before.body.end">' .
                    '<block class="Magento\Framework\View\Element\Text" name="move.image.under.title.on.mobile">' .
                        '<arguments>' .
                            '<argument name="text" xsi:type="string"><![CDATA[' .
                                '<script type="text/javascript">' .
                                    'require([' .
                                        '\'jquery\',' .
                                        '\'matchMedia\'' .
                                    '], function ($, mediaCheck){' .
                                        'mediaCheck({' .
                                            'media: \'(min-width: 768px)\',' .
                                            'entry: function () {' .
                                            '$(\'.product-info-main .product.media\').detach().insertAfter(\'.column.main > .product-info-main\');' .
                                            '},' .
                                            'exit: function () {' .
                                                '$(\'.column.main > .product.media\').detach().insertAfter(\'.product-info-main .page-title-wrapper\');' .
                                            '}' .
                                        '});' .
                                    '});' .
                                '</script>' .
                            ']]></argument>' .
                        '</arguments>' .
                    '</block>' .
                '</referenceContainer>'
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
