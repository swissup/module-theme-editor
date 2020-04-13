<?php

namespace Swissup\ThemeEditor\Model\Config\Source;

class ContentWidth implements \Magento\Framework\Data\OptionSourceInterface
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
            ['value' => 'full_width', 'label' => __('Full width')],
            ['value' => 'limited_width', 'label' => __('Limited width')],
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
