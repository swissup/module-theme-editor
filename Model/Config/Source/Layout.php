<?php

namespace Swissup\ThemeEditor\Model\Config\Source;

class Layout implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => '', 'label' => __('Theme Defined')],
            ['value' => 'themeeditor_page_one_column', 'label' => __('One column')],
            ['value' => 'themeeditor_page_two_columns_left', 'label' => __('Two columns (left sidebar)')],
            ['value' => 'themeeditor_page_two_columns_right', 'label' => __('Two columns (right sidebar)')],
            ['value' => 'themeeditor_page_three_columns', 'label' => __('Three columns')],
        ];
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        $result = [];
        foreach ($this->toOptionArray() as $item) {
            $result[$item['value']] = $item['label'];
        }
        return $result;
    }
}
