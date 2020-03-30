<?php

namespace Swissup\ThemeEditor\Model\Config\Backend;

class ContentWidth extends PageLayout
{
    /**
     * {@inheritdoc}
     */
    public function generateXml()
    {
        $value = $this->getData('value');
        $maxWidth = 'none';
        if ($value === 'limited_width') {
            $maxWidth = $this->getData('fieldset_data/max_width') . 'px';
        }

        return '<body>' .
                '<referenceContainer name="after.body.start">' .
                    '<block class="Magento\Framework\View\Element\Text" name="argento.fullwidth.content" after="-">' .
                        '<arguments>' .
                            '<argument name="text" xsi:type="string">' .
                                "<![CDATA[<style>@media screen and (min-width: 768px) {{$this->getSelector()} {max-width: {$maxWidth};}}</style>]]>" .
                            '</argument>' .
                        '</arguments>' .
                    '</block>' .
                '</referenceContainer>' .
            '</body>';
    }

    /**
     * @return string
     */
    public function getSelector()
    {
        if ($this->hasData('selector')) {
            return $this->getData('selector');
        }

        return '.page-main';
    }
}
