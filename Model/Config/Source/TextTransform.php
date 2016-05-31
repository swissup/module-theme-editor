<?php
namespace Swissup\ThemeEditor\Model\Config\Source;

/**
 * Class Text Transform
 */
class TextTransform implements \Magento\Framework\Option\ArrayInterface
{
    protected $_values = [
        'none'       => 'none',
        'uppercase'  => 'uppercase',
        'capitalize' => 'capitalize',
        'lowercase'  => 'lowercase'
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
