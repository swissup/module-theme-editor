<?php

namespace Swissup\ThemeEditor\Model\Config\Backend\Category;

use Swissup\ThemeEditor\Model\Config\Backend\PageLayout;

class MoveLayeredNavigation extends PageLayout
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
            // move layered navigation before products
            'content.before' => '<referenceContainer name="content.top">' .
                                '<container name="argento.emulate1columFilters" htmlTag="div" htmlClass="page-layout-1column emulate-1column-filters">' .
                                '<block class="Magento\Framework\View\Element\Template" name="argento.filterWrapper" template="Magento_Theme::layered-navigation/filter-wrapper.phtml" before="-" />' .
                                '</container>' .
                                '</referenceContainer>' .
                                '<move element="catalog.leftnav" destination="argento.filterWrapper" after="-"/>',
            // move layered navigation to sidebar
            'sidebar' => '<move element="catalog.leftnav" destination="sidebar.main" before="-" />',
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
