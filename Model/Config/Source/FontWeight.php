<?php
namespace Swissup\ThemeEditor\Model\Config\Source;

/**
 * Class Font Weight
 */
class FontWeight implements \Magento\Framework\Option\ArrayInterface
{
    protected $_values = [
        'normal' => 'normal',
        'bold'   => 'bold'
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
