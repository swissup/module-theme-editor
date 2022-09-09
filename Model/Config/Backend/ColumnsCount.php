<?php

namespace Swissup\ThemeEditor\Model\Config\Backend;

class ColumnsCount extends PageLayout
{
    /**
     * @var \Magento\Framework\Math\Random
     */
    protected $mathRandom;

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->mathRandom = $this->getData('mathRandom');
        $this->unsetData('mathRandom');

        parent::_construct();
    }

    /**
     * Process data after load
     *
     * @return void
     */
    protected function _afterLoad()
    {
        $value = $this->getValue();
        $value = $this->makeArrayFieldValue($value);
        $this->setValue($value);
    }

    /**
     * Prepare data before save
     *
     * @return void
     */
    public function beforeSave()
    {
        $value = $this->getValue();
        $value = $this->makeStorableArrayFieldValue($value);
        $this->setValue($value);
    }

    /**
     * Check whether value is in form retrieved by _encodeArrayFieldValue()
     *
     * @param string|array $value
     * @return bool
     */
    protected function isEncodedArrayFieldValue($value)
    {
        if (!is_array($value)) {
            return false;
        }
        unset($value['__empty']);
        foreach ($value as $row) {
            if (!is_array($row)
                || !array_key_exists('width', $row)
                || !array_key_exists('columns', $row)
                || !array_key_exists('spacing', $row)
            ) {
                return false;
            }
        }
        return true;
    }

    /**
     * Decode value from used in \Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray
     *
     * @param array $value
     * @return array
     */
    protected function decodeArrayFieldValue(array $value)
    {
        $result = [];
        unset($value['__empty']);
        foreach ($value as $row) {
            if (!is_array($row)
                || !array_key_exists('width', $row)
                || !array_key_exists('columns', $row)
                || !array_key_exists('spacing', $row)
            ) {
                continue;
            }

            $width = $row['width'];
            unset($row['width']);
            $result[$width] = $row;
        }

        ksort($result, SORT_NUMERIC);

        return $result;
    }

    /**
     * Generate a storable representation of a value
     *
     * @param int|float|string|array $value
     * @return string
     */
    protected function serializeValue($value)
    {
        if (is_numeric($value)) {
            $data = (float) $value;
            return (string) $data;
        } elseif (is_array($value)) {
            $data = [];
            foreach ($value as $width => $settings) {
                $data[$width] = $settings;
            }

            return json_encode($data);
        } else {
            return '';
        }
    }

    /**
     * Make value ready for store
     *
     * @param string|array $value
     * @return string
     */
    public function makeStorableArrayFieldValue($value)
    {
        if ($this->isEncodedArrayFieldValue($value)) {
            $value = $this->decodeArrayFieldValue($value);
        }
        $value = $this->serializeValue($value);
        return $value;
    }

    /**
     * Make value readable by \Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray
     *
     * @param string|array $value
     * @return array
     */
    public function makeArrayFieldValue($value)
    {
        $value = $this->unserializeValue($value);
        if (!$this->isEncodedArrayFieldValue($value)) {
            $value = $this->encodeArrayFieldValue($value);
        }
        return $value;
    }

    /**
     * Create a value from a storable representation
     *
     * @param int|float|string $value
     * @return array
     */
    protected function unserializeValue($value)
    {
        $decodeAsArray = true;
        if (is_string($value) && !empty($value)) {
            return json_decode($value, $decodeAsArray);
        } else {
            return [];
        }
    }

    /**
     * Encode value to be used in \Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray
     *
     * @param array $value
     * @return array
     */
    protected function encodeArrayFieldValue(array $value)
    {
        $result = [];
        foreach ($value as $width => $settings) {
            $resultId = $this->mathRandom->getUniqueHash('_');
            $result[$resultId] = ['width' => $width] + $settings;
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function generateXml()
    {
        $value = $this->getValue();
        $value = $this->makeArrayFieldValue($value);

        if (!is_array($value)) {
            return '';
        }

        $selector = implode(', ', [
                'body[class].page-products .products-grid .product-item',
                'body[class] .products-grid .product-item',
                'body[class].wishlist-index-index .products-grid .product-item',
                'body[class] .block.widget .products-grid .product-item'
            ]);
        $selectorSpacing = 'body[class] .column.main .products-grid .product-item:nth-child(n)';
        $mediaQueries = '';
        foreach ($value as $item) {
            $columns = (int)$item['columns'];
            $spacing = (int)$item['spacing'];
            if (!$columns) {
                continue;
            }

            $width = $this->getItemWidthValue($columns, $spacing);
            $mediaQueries .= "@media screen and (min-width: {$item['width']}px) {" .
                "{$selector} { width: {$width} }" .
                "{$selectorSpacing} { margin: 0 0 {$spacing}px {$spacing}px }" .
            "}\n";
        }

        return '<body>' .
                '<referenceContainer name="after.body.start">' .
                    '<block class="Magento\Framework\View\Element\Text" name="style.category.grid.columns" after="-">' .
                        '<arguments>' .
                            '<argument name="text" xsi:type="string">' .
                                "<![CDATA[<style>{$mediaQueries}</style>]]>" .
                            '</argument>' .
                        '</arguments>' .
                    '</block>' .
                '</referenceContainer>' .
            '</body>';
    }

    /**
     * @param  string $columns
     * @param  string $spacing
     * @return string
     */
    protected function getItemWidthValue($columns, $spacing)
    {
        return "calc(100% / {$columns} - {$spacing}px - 0.1px)";
    }
}
