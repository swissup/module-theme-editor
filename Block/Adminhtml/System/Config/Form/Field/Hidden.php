<?php
namespace Swissup\ThemeEditor\Block\Adminhtml\System\Config\Form\Field;

use Magento\Framework\Data\Form\Element\AbstractElement;

class Hidden extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * {@inheritdoc}
     */
    protected function _decorateRowHtml(AbstractElement $element, $html)
    {
        return '<tr id="row_'
            . $element->getHtmlId()
            . '" style="display: none">'
            . $html
            . '</tr>';
    }

    /**
     * {@inheritdoc}
     */
    protected function _isInheritCheckboxRequired(AbstractElement $element)
    {
        return false;
    }
}
