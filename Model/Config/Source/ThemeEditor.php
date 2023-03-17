<?php

namespace Swissup\ThemeEditor\Model\Config\Source;

class ThemeEditor implements \Magento\Framework\Data\OptionSourceInterface
{
    private $cssModel;
    /**
     * @param \Swissup\ThemeEditor\Model\Css $cssModel
     */
    public function __construct(\Swissup\ThemeEditor\Model\Css $cssModel)
    {
        $this->cssModel = $cssModel;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $result = [['value' => '', 'label' => __('Auto')]];
        $themes = $this->cssModel->getThemesList();

        foreach ($themes as $themeCode) {
            $parts = explode('_', $themeCode);
            array_shift($parts); // remove vendor name

            $result[] = [
                'value' => $themeCode,
                'label' => ucwords(implode(' ', $parts)),
            ];
        }

        return $result;
    }
}
