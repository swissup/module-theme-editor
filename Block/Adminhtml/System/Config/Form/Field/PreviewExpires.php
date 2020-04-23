<?php
namespace Swissup\ThemeEditor\Block\Adminhtml\System\Config\Form\Field;

use Magento\Framework\Data\Form\Element\AbstractElement;

class PreviewExpires extends Hidden
{
    /**
     * {@inheritdoc}
     */
    public function render(AbstractElement $element)
    {
        $currentTime = time();
        $element->setValue($currentTime + 10 * 60); // current time + 10min

        return parent::render($element);
    }
}
