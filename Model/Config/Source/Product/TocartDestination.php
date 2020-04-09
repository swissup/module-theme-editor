<?php

namespace Swissup\ThemeEditor\Model\Config\Source\Product;

class TocartDestination implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => '', 'label' => __('Specified by theme')],
            ['value' => 'product.info.main', 'label' => __('Product Info Main')],
            ['value' => 'product.info.media', 'label' => __('Product Media (bottom)')],
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
