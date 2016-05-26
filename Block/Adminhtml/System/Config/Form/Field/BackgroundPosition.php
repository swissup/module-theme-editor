<?php
namespace Swissup\ThemeEditor\Block\Adminhtml\System\Config\Form\Field;

class BackgroundPosition extends \Magento\Config\Block\System\Config\Form\Field {

    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element) {
        $element->setStyle('width:70px;')
            ->setName($element->getName() . '[]');

        if ($element->getValue()) {
            $values = explode(',', $element->getValue());
        } else {
            $values = [];
        }

        $left = $element->setValue(isset($values[0]) ? $values[0] : null)->getElementHtml();
        $top  = $element->setValue(isset($values[1]) ? $values[1] : null)->getElementHtml();
        return __('left') . ' ' . $left . ' ' . __('top') . ' ' . $top;
    }
}
