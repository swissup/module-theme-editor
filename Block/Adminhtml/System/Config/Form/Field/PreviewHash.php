<?php
namespace Swissup\ThemeEditor\Block\Adminhtml\System\Config\Form\Field;

use Magento\Framework\Data\Form\Element\AbstractElement;

class PreviewHash extends Hidden
{
    /**
     * {@inheritdoc}
     */
    public function render(AbstractElement $element)
    {
        $element->setValue($this->mathRandom->getUniqueHash());

        return parent::render($element);
    }
}
