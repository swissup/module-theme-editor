<?php
namespace Swissup\ThemeEditor\Block\Adminhtml\System\Config\Form\Field;

use Magento\Framework\Data\Form\Element\Renderer\RendererInterface;
use Magento\Backend\Block\AbstractBlock;
use Magento\Framework\Data\Form\Element\AbstractElement;

class PagePreview extends AbstractBlock implements RendererInterface
{
    /**
     * @param \Magento\Backend\Block\Context $context
     * @param array                          $previewData
     * @param array                          $data
     */
    public function __construct(
        \Magento\Backend\Block\Context $context,
        array $previewData = [],
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->setData('preview_data', $previewData);
    }

    /**
     * Render form element as HTML
     *
     * @param  AbstractElement $element
     * @return string
     */
    public function render(AbstractElement $element)
    {
        $data = $this->getPreviewData();
        $data['element'] = $element;
        $this->addChild(
            'preview',
            \Magento\Framework\View\Element\Template::class,
            $data
        );

        $previewHtml = $this->getChildHtml('preview');

        return sprintf(
            '<tr id="row_%s"><td class="label">%s</td><td class="value">%s</td><td colspan="3"></td></tr>',
            $element->getHtmlId(),
            $element->getLabel(),
            $previewHtml
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareLayout()
    {
        $this->getBlockStyles()->addData($this->getPreviewData());

        return parent::_prepareLayout();
    }

    /**
     * Get block with styles.
     *
     * @return \Magento\Framework\View\Element\Template
     */
    private function getBlockStyles()
    {
        $name = 'swissup.themeeditor.preview.styles';
        if ($block = $this->getLayout()->getBlock($name)) {
            return $block;
        }

        $block = $this->getLayout()->addBlock(
            \Magento\Framework\View\Element\Template::class,
            'swissup.themeeditor.preview.styles',
            'before.body.end'
        )->setTemplate('Swissup_ThemeEditor::config/styles.phtml');

        return $block;
    }
}
