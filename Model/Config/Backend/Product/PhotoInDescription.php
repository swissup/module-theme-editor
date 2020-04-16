<?php

namespace Swissup\ThemeEditor\Model\Config\Backend\Product;

use Swissup\ThemeEditor\Model\Config\Backend\PageLayout;

class PhotoInDescription extends PageLayout
{
    /**
     * @var array
     */
    protected $mapping = [];

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $target = $this->getTarget();
        $this->mapping = [
            'yes' => '<referenceBlock name="description.tabbed.add.photo">' .
                    '<arguments>' .
                        '<argument name="options" xsi:type="array">' .
                            '<item name="target" xsi:type="string">' . $target . '</item>' .
                        '</argument>' .
                    '</arguments>'.
                '</referenceBlock>',
            'no' => '<referenceBlock name="description.tabbed.add.photo" remove="true" />'
        ];

        parent::_construct();
    }

    /**
     * {@inheritdoc}
     */
    public function generateXml()
    {
        $value = $this->getData('value');

        return $this->mapping[$value] ?? '';
    }

    /**
     * @return string
     */
    public function getTarget()
    {
        if ($this->hasData('target')) {
            return $this->getData('target');
        }

        return '#product\\\\.info\\\\.description[data-role=content]';
    }
}
