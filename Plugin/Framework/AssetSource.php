<?php

namespace Swissup\ThemeEditor\Plugin\Framework;

class AssetSource
{
    const FILENAME = '_theme-editor.less';

    const PLACEHOLDER = '// @swissup-theme-editor';

    /**
     * @var \Swissup\ThemeEditor\Helper\Data
     */
    private $helper;

    /**
     * @var \Magento\Framework\View\Design\Theme\ThemeProviderInterface
     */
    private $themeProvider;

    public function __construct(
        \Swissup\ThemeEditor\Helper\Data $helper,
        \Magento\Framework\View\Design\Theme\ThemeProviderInterface $themeProvider
    ) {
        $this->helper = $helper;
        $this->themeProvider = $themeProvider;
    }

    /**
     * @param \Magento\Framework\View\Asset\Source $subject
     * @param string $result
     * @return string
     */
    public function afterGetContent(
        \Magento\Framework\View\Asset\Source $subject,
        $result,
        \Magento\Framework\View\Asset\LocalInterface $asset
    ) {
        if (!$result || strpos($result, self::PLACEHOLDER) === false) {
            return $result;
        }

        $theme = $this->themeProvider->getThemeByFullPath(
            $asset->getContext()->getAreaCode()
            . '/'
            . $asset->getContext()->getThemePath()
        );

        $result = str_replace(
            self::PLACEHOLDER,
            $this->helper->getLessStyles($theme),
            $result
        );

        return $result;
    }
}
