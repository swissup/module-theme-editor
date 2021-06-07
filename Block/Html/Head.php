<?php
namespace Swissup\ThemeEditor\Block\Html;

use \Magento\Framework\UrlInterface;

class Head extends \Magento\Framework\View\Element\Template implements
    \Magento\Framework\DataObject\IdentityInterface
{
    /**
     * @var \Swissup\ThemeEditor\Model\Css
     */
    private $cssModel;

    /**
     * @var \Swissup\ThemeEditor\Helper\Data
     */
    private $helper;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Swissup\ThemeEditor\Model\CssFactory $cssModelFactory
     * @param \Swissup\ThemeEditor\Helper\Data $helper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Swissup\ThemeEditor\Model\CssFactory $cssModelFactory,
        \Swissup\ThemeEditor\Helper\Data $helper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->cssModel = $cssModelFactory->create();
        $this->helper = $helper;
    }
    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->addData(
            [
                'cache_lifetime' => 86400
            ]
        );
    }
    /**
     * Get key pieces for caching block content
     *
     * @return array
     */
    public function getCacheKeyInfo()
    {
        return [
            'SWISSUP_THEME_EDITOR',
            $this->_storeManager->getStore()->getId(),
            $this->_storeManager->getStore()->isCurrentlySecure(),
            $this->_design->getDesignTheme()->getId()
        ];
    }
    /**
     * Render block HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        return $this->getBackendCss() ?
            '<link rel="stylesheet" property="stylesheet" type="text/css" href="' .
            $this->getBackendCss() .
            '" media="all" />' : '';
    }
    /**
     * Finds backend.css to use for current theme
     *
     * Fallback rules are used to support Magento's configuration descending:
     *  pub/media/[package]/[theme]/[website_store]_backend.css
     *  pub/media/[package]/[theme]/[website]_backend.css
     *  pub/media/[package]/[theme]/0_backend.css
     *
     * @return string of false
     */
    public function getBackendCss()
    {
        $theme = $this->getLayout()->getUpdate()->getTheme()->getCode();
        $theme = strtolower(str_replace(['/', '-'], '_', $theme));
        $storeCode  = $this->_storeManager->getStore()->getCode();
        $websiteCode = $this->_storeManager->getWebsite()->getCode();
        $mediaDir = $this->getMediaDirectory()->getAbsolutePath();

        // manually selected theme editor
        $editor = $this->helper->getThemeEditorCode();
        if ($editor) {
            $theme = $editor;
        }

        $args = [
            [$theme, $storeCode, $websiteCode],
            [$theme, null, $websiteCode],
            [$theme, null, null]
        ];

        foreach ($args as $_args) {
            $filePath = $this->cssModel->getFilePath($_args[0], $_args[1], $_args[2]);

            if (file_exists($mediaDir . $filePath)) {
                $url = $this->_urlBuilder->getBaseUrl(
                    ['_type' => UrlInterface::URL_TYPE_MEDIA]
                ) . $filePath . '?version=' . filemtime($mediaDir . $filePath);
                $objectBackendCss = new \Magento\Framework\DataObject([
                    'url' => $url,
                    'media_dir' => $mediaDir,
                    'file_path' => $filePath
                ]);
                $this->_eventManager->dispatch(
                    strtolower($theme) . '_block_head_backend_css_after',
                    ['backend_css' => $objectBackendCss]
                );
                return $objectBackendCss->getUrl();
            }
        }
        return false;
    }
    /**
     * Return identifiers for produced content
     *
     * @return array
     */
    public function getIdentities()
    {
        return ['theme_editor_backend_css_block'];
    }
}
