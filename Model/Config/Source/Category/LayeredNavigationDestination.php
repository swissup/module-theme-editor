<?php

namespace Swissup\ThemeEditor\Model\Config\Source\Category;

class LayeredNavigationDestination implements \Magento\Framework\Data\OptionSourceInterface
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
            ['value' => 'content.before', 'label' => __('Before products list into main column')],
            ['value' => 'sidebar', 'label' => __('Sidebar')],
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
