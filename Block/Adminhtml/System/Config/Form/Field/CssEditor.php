<?php
namespace Swissup\ThemeEditor\Block\Adminhtml\System\Config\Form\Field;

use Magento\Framework\Data\Form\Element\AbstractElement;
use Swissup\CheckoutSuccess\Model\Config\Source\AvailableBlocks;

class CssEditor extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * Path to template file.
     *
     * @var string
     */
    protected $_template = 'css-editor.phtml';

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
     * Get options to initialize javascript
     *
     * @return string
     */
    public function getOptions()
    {
        return json_encode(
            [
                'disabled' => $this->getElement()->getData('disabled')
            ]
        );
    }
}
