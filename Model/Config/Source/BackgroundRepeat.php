<?php
namespace Swissup\ThemeEditor\Model\Config\Source;

use Magento\Framework\View\Model\PageLayout\Config\BuilderInterface;

/**
 * Class background repeat
 */
class BackgroundRepeat implements \Magento\Framework\Option\ArrayInterface
{
    protected $_values = [
        'no-repeat' => 'no-repeat',
        'repeat'    => 'repeat',
        'repeat-x'  => 'repeat-x',
        'repeat-y'  => 'repeat-y'
    ];
    /**
     * To option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        $result  = [];
        foreach ($this->_values as $value => $label) {
            $result[] = [
                'value' => $value,
                'label' => $label
            ];
        }
        return $result;
    }
}
