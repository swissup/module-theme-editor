<?php

namespace Swissup\ThemeEditor\Model\Config\Backend\Product;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\ProductMetadataInterface;
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
        // see https://github.com/magento/magento2/pull/39391
        $after = $this->compareMagentoVersion('2.4.7', '<') ? 'product.info.media' : 'product.info.main';

        $this->mapping = [
            'content' => '<move element="product.info.tabs" destination="content" after="' . $after . '" />',
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

    private function compareMagentoVersion($version, $operator)
    {
        $metadata = ObjectManager::getInstance()->get(ProductMetadataInterface::class);

        return version_compare($metadata->getVersion(), $version, $operator);
    }
}
