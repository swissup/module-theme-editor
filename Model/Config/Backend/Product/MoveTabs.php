<?php

namespace Swissup\ThemeEditor\Model\Config\Backend\Product;

use Swissup\ThemeEditor\Model\Config\Backend\PageLayout;

class MoveTabs extends PageLayout
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
            'content' => '<move element="product.info.tabs" destination="content" after="product.info.media" />',
            'product.info.main' => '<move element="product.info.tabs" destination="product.info.main" after="-" />'
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
