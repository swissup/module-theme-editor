<?php
namespace Swissup\ThemeEditor\Block\Adminhtml\System\Config\Form\Field;

class CssSelectorHeading extends \Magento\Backend\Block\AbstractBlock implements
    \Magento\Framework\Data\Form\Element\Renderer\RendererInterface
{
    /**
     * @var \Swissup\ThemeEditor\Helper\Helper
     */
    protected $helper;
    /**
     * @param \Magento\Backend\Block\Context $context
     * @param \Swissup\ThemeEditor\Helper\Helper $helper
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Context $context,
        \Swissup\ThemeEditor\Helper\Helper $helper,
        array $data = []
    )
    {
        parent::__construct($context, $data);
        $this->_authorization = $context->getAuthorization();
        $this->helper = $helper;
    }
    /**
     * Render form element as HTML
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        if ($selector = $this->_getCssSelector($element)) {
            return sprintf('<tr id="row_%s"><td colspan="5"><h4 id="%s">%s</h4></td></tr>',
                $element->getHtmlId(), $element->getHtmlId(), ($element->getLabel() . ' (' . $selector . ')')
            );
        } else {
            return sprintf('<tr id="row_%s"><td colspan="5"><h4 id="%s">%s</h4></td></tr>',
                $element->getHtmlId(), $element->getHtmlId(), $element->getLabel()
            );
        }
    }

    protected function _getCssSelector($element)
    {
        $parts = explode('_', $element->getId());
        array_pop($parts);
        $node = $this->helper->camel2dashed(array_pop($parts));
        $section = explode('/', $element->getFieldConfig('path'))[0];
        $selector = $this->_scopeConfig->getValue(
            $section . '/css_selector/' . $node,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        if (!empty($selector)) {
            return $selector;
        }
        return false;
    }
}
