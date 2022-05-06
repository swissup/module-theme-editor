<?php
namespace Swissup\ThemeEditor\Block\Adminhtml\System\Config\Form\Field;

use Magento\Framework\Data\Form\Element\AbstractElement;

class Available extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * Path to template file.
     *
     * @var string
     */
    protected $_template = 'config-field/available.phtml';

    /**
     * @var \Swissup\ThemeEditor\Helper\Header
     */
    protected $helper;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Swissup\ThemeEditor\Helper\Header $helper
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Swissup\ThemeEditor\Helper\Header $helper,
        array $data = []
    ) {
        $this->helper = $helper;
        parent::__construct($context, $data);
    }

    /**
     * Render element HTML
     *
     * @param  AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        $this->setElement($element);

        return $this->toHtml();
    }

    /**
     * Check if inheritance checkbox has to be rendered
     *
     * @param AbstractElement $element
     * @return bool
     */
    protected function _isInheritCheckboxRequired(AbstractElement $element)
    {
        return false;
    }

    /**
     * Get list of available blocks not used in layout
     *
     * @return array
     */
    public function getAvailableOptions()
    {
        $options = $this->getElement()->getValues();
        $config = $this->helper->getHeaderLayout();

        $usedBlocks = [];
        foreach ($config as $value) {
            $usedBlocks = array_merge($usedBlocks, array_column($value['children'], 'name'));
        }

        $available = [];
        foreach ($options as $option) {
            if (!in_array($option['value'], $usedBlocks)) {
                $available[] = $option;
            }
        }

        return $available;
    }
}
