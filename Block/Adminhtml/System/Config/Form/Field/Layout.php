<?php
namespace Swissup\ThemeEditor\Block\Adminhtml\System\Config\Form\Field;

use Magento\Framework\Data\Form\Element\AbstractElement;
use Swissup\ThemeEditor\Model\Config\Source\AvailableHeaderBlocks;

class Layout extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * Path to template file.
     *
     * @var string
     */
    protected $_template = 'config-field/layout.phtml';

    /**
     * @var AvailableHeaderBlocks
     */
    protected $availableBlocks;

    /**
     * @param AvailableHeaderBlocks $availableBlocks
     * @param \Magento\Backend\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        AvailableHeaderBlocks $availableBlocks,
        \Magento\Backend\Block\Template\Context $context,
        array $data = []
    ) {
        $this->availableBlocks = $availableBlocks;
        return parent::__construct($context, $data);
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
     * {@inheritdoc}
     */
    public function render(AbstractElement $element)
    {
        $html = parent::render($element);
        // Do not render label. And make value take two cells.
        $labelHtml = '<td class="label"><label for="' .
            $element->getHtmlId() . '"><span' .
            $this->_renderScopeLabel($element) . '>' .
            $element->getLabel() .
            '</span></label></td>';

        $html = str_replace($labelHtml, '', $html, $count);
        if ($count > 0) {
            $html = str_replace(
                '<td class="value">',
                '<td class="value" colspan="3" style="padding: 2.2rem 2rem 2.2rem 2.8rem; width: 100%">',
                $html
            );
        }

        return $html;
    }

    /**
     * Get options to initialize javascript
     *
     * @return string
     */
    public function getOptions()
    {
        return json_encode(
            [
                'disabled' => $this->getElement()->getData('disabled'),
                'parentId' => $this->getElement()->getContainer()->getHtmlId(),
                'availableBlocks' => $this->availableBlocks->toOptions(),
            ]
        );
    }
}
