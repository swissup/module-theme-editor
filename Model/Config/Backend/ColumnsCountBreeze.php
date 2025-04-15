<?php

namespace Swissup\ThemeEditor\Model\Config\Backend;

class ColumnsCountBreeze extends ColumnsCount
{
    public function generateXml()
    {
        $value = $this->getValue();
        $value = $this->makeArrayFieldValue($value);

        if (!is_array($value)) {
            return '';
        }

        $selector ='body';
        $mediaQueries = '';
        foreach ($value as $item) {
            $columns = (int)$item['columns'];
            $spacing = (int)$item['spacing'];
            if (!$columns) {
                continue;
            }

            $mediaQueries .= "@media screen and (min-width: {$item['width']}px) {" .
                "{$selector} { --listing-grid-columns: {$columns}; --listing-grid-gap: {$spacing}px; }" .
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
}
