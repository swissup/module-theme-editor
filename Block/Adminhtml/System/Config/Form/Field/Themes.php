<?php

namespace Swissup\ThemeEditor\Block\Adminhtml\System\Config\Form\Field;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

class Themes extends Field
{
    /**
     * @var string
     */
    protected $_template = 'config-field/themes.phtml';

    /**
     * Render element HTML
     *
     * @param  AbstractElement $element
     * @return string
     */
    public function render(AbstractElement $element)
    {
        return $this->toHtml();
    }
}
