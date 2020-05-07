<?php

namespace Swissup\ThemeEditor\Model\Config\Backend;

use Magento\Widget\Model\Layout;

abstract class AbstractLayout extends \Magento\Framework\App\Config\Value
{
    /**
     * @var \Magento\Framework\App\Config\ValueFactory
     */
    protected $configValueFactory;

    /**
     * @var \Magento\Theme\Model\ThemeFactory
     */
    protected $themeFactory;

    /**
     * @var string
     */
    protected $themeCode;

    /**
     * @var Layout\LinkFactory
     */
    protected $layoutLinkFactory;

    /**
     * @var Layout\UpdateFactory
     */
    protected $layoutUpdateFactory;

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->themeFactory = $this->getData('themeFactory');
        $this->themeCode = $this->getData('themeCode');
        $this->configValueFactory = $this->getData('configValueFactory');
        $this->layoutLinkFactory = $this->getData('layoutLinkFactory');
        $this->layoutUpdateFactory = $this->getData('layoutUpdateFactory');
        $this->unsetData('themeFactory');
        $this->unsetData('themeCode');
        $this->unsetData('configValueFactory');
        $this->unsetData('layoutLinkFactory');
        $this->unsetData('layoutUpdateFactory');

        parent::_construct();
    }

    /**
     * Get sort order for XML layout
     *
     * @param  string $scope
     * @return int
     */
    public function getOrder($scope)
    {
        $order = 0;
        switch ($scope) {
            case 'stores':
                $order = 20;
                break;
            case 'websites':
                $order = 10;
                break;
            default:
                $order = 0;
                break;
        }

        return $order;
    }

    public function getLayoutLink($id)
    {
        return $this->layoutLinkFactory->create()->load($id);
    }

    /**
     * Delete layout update from DB
     *
     * @param  Layout\Link $layoutLink
     * @return void
     */
    public function deleteLayout(Layout\Link $layoutLink)
    {
        if (!$layoutLink->getLayoutUpdateId()) {
            return;
        }

        // delete layout records
        // Layout updates table has foring key with casecade on delete
        $this->layoutUpdateFactory->create()
            ->load($layoutLink->getLayoutUpdateId())
            ->delete();

        // After removing layout it is necessary to invalidate layout cache.
        $this->cacheTypeList->invalidate(\Magento\Framework\App\Cache\Type\Layout::TYPE_IDENTIFIER);
    }

    /**
     * Update existing or insert new layout update into DB.
     *
     * @param  Layout\Link $layoutLink
     * @param  string      $handle
     * @param  string      $xml
     * @return Layout\Link
     * @throws \Exception
     */
    public function updateLayout(
        Layout\Link $layoutLink,
        $handle,
        $xml
    ) {
        if (!$this->themeCode) {
            throw new \Exception(__('Property $themeCode must be specified.'));
        }

        $scope = $this->getData('scope');
        $scopeId = $this->getData('scope_id');
        $theme = $this->themeFactory->create()->load($this->themeCode, 'code');

        $layoutUpdate = $this->layoutUpdateFactory->create()
            ->load($layoutLink->getLayoutUpdateId())
            ->setHandle($handle)
            ->setXml($xml)
            ->setSortOrder($this->getOrder($scope))
            ->save();

        $layoutLink->setStoreId($scopeId)
            ->setThemeId($theme->getId())
            ->setLayoutUpdateId($layoutUpdate->getId())
            ->save();

        return $layoutLink;
    }
}
