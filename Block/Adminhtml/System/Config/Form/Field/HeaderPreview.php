<?php
namespace Swissup\ThemeEditor\Block\Adminhtml\System\Config\Form\Field;

use Magento\Framework\Data\Form\Element\AbstractElement;

class HeaderPreview extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * @var \Magento\Framework\Data\Form\ElementFactory
     */
    protected $elementFactory;

    /**
     * @var string
     */
    protected $previewHtmlId = 'row_swissup_argento_header_config_preview_iframe';

     /**
     * @param \Magento\Framework\Data\Form\ElementFactory $elementFactory
     * @param \Magento\Backend\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Data\Form\ElementFactory $elementFactory,
        \Magento\Backend\Block\Template\Context $context,
        array $data = []
    ) {
        $this->elementFactory = $elementFactory;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve HTML markup for given form element
     *
     * @param AbstractElement $element
     * @return string
     */
    public function render(AbstractElement $element)
    {
        return parent::render($element)
            . '<tr id="' . $this->previewHtmlId . '">'
            . '<td colspan="3" class="value">'
            . '<div data-role="iframe-placeholder" style="display: none;">'
            . '<div data-role="spinner" class="admin__data-grid-loading-mask">'
            . '<div class="spinner">'
            . '<span></span><span></span><span></span><span></span>'
            . '<span></span><span></span><span></span><span></span>'
            . '</div>'
            . '</div>'
            . '</div>'
            . '</td>'
            . '</tr>';
    }

    /**
     * Render element HTML
     *
     * @param  AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        // add button to show preview
        $button = $this->getLayout()
            ->createBlock('Magento\Backend\Block\Widget\Button')
            ->setLabel(__('Preview Header'))
            ->setId('theme_editor_header__preview')
            ->setAfterHtml('<div class="note">' .
                __('Preview header configuration before save.<br>Make sure you preview the store view where the theme installed.') .
                '</div>'
            )
            ->setDataAttribute([
                'mage-init' => [
                    'Swissup_ThemeEditor/js/header/preview' => [
                        'target' => '#' . $this->previewHtmlId . ' td',
                        'previewUrl' => $this->getPreviewUrl(),
                        'source' => '#row_' . $element->getHtmlId() . ' td.value'
                    ]
                ]
            ]);

        return parent::_getElementHtml($element) . $button->toHtml();
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
     * Get URL to preview configurable header
     *
     * @return string
     */
    public function getPreviewUrl()
    {
        $store = $this->getStoreInConfig();
        $url = ['swissupeditor', 'header', 'preview', 'hash', '{{previewHash}}'];

        return $store->getBaseUrl()
            . implode('/', $url)
            . '/?___store=' . $store->getCode();
    }

    /**
     * Get store from config form
     *
     * @return \Magento\Store\Api\Data\StoreInterface|null
     */
    public function getStoreInConfig()
    {
        $form = $this->getForm();
        if ($form->getStoreCode()) {
            $store = $this->_storeManager->getStore($form->getStoreCode());
        } elseif ($form->getWebsiteCode()) {
            $store = $this->_storeManager->getWebsite($form->getWebsiteCode())
                ->getDefaultStore();
        } else {
            $store = $this->_storeManager->getDefaultStoreView();
        }

        return $store;
    }
}
