<?php

namespace Swissup\ThemeEditor\Block\Adminhtml\System\Config\Form\Field;

class ColumnsCount extends \Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray
{
    /**
     * Prepare to render
     *
     * @return void
     */
    protected function _prepareToRender()
    {
        $this->addColumn('width', ['label' => __('Min width, px')]);
        $this->addColumn('columns', ['label' => __('Columns')]);
        $this->addColumn('spacing', ['label' => __('Spacing, px')]);
        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add columns exception');
    }
}
