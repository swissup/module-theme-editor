<?php

namespace Swissup\ThemeEditor\Model\Config\Source\Category;

class DescriptionDestination implements \Magento\Framework\Data\OptionSourceInterface
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
            ['value' => 'content.after', 'label' => __('After products list into main column')],
            ['value' => 'main.content.after', 'label' => __('After all columns before footer')],
            ['value' => 'content.before', 'label' => __('Before products list into main column')],
            ['value' => 'main.content.before', 'label' => __('Before all columns after title')],
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
