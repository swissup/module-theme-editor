<?php

namespace Swissup\ThemeEditor\Model\Config\Backend;

class PageLayout extends AbstractLayout
{
    /**
     * @var string
     */
    protected $handle;

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->handle = $this->getData('handle');
        $this->unsetData('handle');

        parent::_construct();
    }

    /**
     * @return $this
     */
    public function afterSave()
    {
        $value = $this->getData('value');
        $scope = $this->getData('scope');
        $scopeId = $this->getData('scope_id');
        $path = $this->getData('path');

        $layoutLinkPath = $path . '_link_id';
        $layoutLinkId = $this->_config->getValue(
            $layoutLinkPath,
            $scope,
            $scopeId
        );

        $layoutLink = $this->getLayoutLink($layoutLinkId);

        if (empty($value)) {
            $this->deleteLayout($layoutLink);
        } else {
            // insert/update xml layout
            $this->updateLayout($layoutLink, $this->handle, $this->generateXml());
        }

        $this->configValueFactory->create()
            ->setScope($scope)
            ->setScopeId($scopeId)
            ->setPath($layoutLinkPath)
            ->setValue(empty($value) ? '' : $layoutLink->getId())
            ->save();

        return parent::afterSave();
    }

    /**
     * @return $this
     */
    public function afterDelete()
    {
        $scope = $this->getData('scope');
        $scopeId = $this->getData('scope_id');
        $path = $this->getData('path');

        $layoutLinkPath = $path . '_link_id';
        $layoutLinkId = $this->_config->getValue(
            $layoutLinkPath,
            $scope,
            $scopeId
        );

        $layoutLink = $this->getLayoutLink($layoutLinkId);
        $this->deleteLayout($layoutLink);
        $this->configValueFactory->create()
            ->setScope($scope)
            ->setScopeId($scopeId)
            ->setPath($layoutLinkPath)
            ->save() // this triggers resource model beforeSave
                     // where _checkUnique sets ID of existing config
            ->delete();

        return parent::afterDelete();
    }

    /**
     * Generate XML layout update.
     *
     * @return string
     */
    public function generateXml()
    {
        $value = $this->getData('value');

        return "<update handle=\"{$value}\" />";
    }
}
