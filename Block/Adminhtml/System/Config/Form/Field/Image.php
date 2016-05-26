<?php
namespace Swissup\ThemeEditor\Block\Adminhtml\System\Config\Form\Field;

class Image extends \Magento\Config\Block\System\Config\Form\Field {

    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        if ('none' === (string)$element->getValue()) {
            $element->setValue(''); // fix to prevent activating of 'Use default' checkbox, when image is deleted
        } else {
            $theme     = $this->getRequest()->getParam('section');
            $baseUrl   = str_replace('_', '/', $theme) . '/images';
            $config    = $element->getFieldConfig();
            $config['base_url'] = [
                'value' => $baseUrl,
                'type' => 'media',
                'scope_info' => 0
            ];
            $element->setFieldConfig($config);
        }
        return $element->getElementHtml();
    }
}
