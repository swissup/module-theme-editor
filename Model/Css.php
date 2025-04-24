<?php
namespace Swissup\ThemeEditor\Model;

use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

class Css
{
    /** Themes */
    const ABSOLUTE_THEME  = 'swissup_absolute';
    const ARGENTO_CHIC    = 'swissup_argento_chic';
    const ARGENTO_ESSENCE = 'swissup_argento_essence';
    const ARGENTO_FLAT    = 'swissup_argento_flat';
    const ARGENTO_FORCE   = 'swissup_argento_force';
    const ARGENTO_HOME    = 'swissup_argento_home';
    const ARGENTO_LUXURY  = 'swissup_argento_luxury';
    const ARGENTO_MALL    = 'swissup_argento_mall';
    const ARGENTO_MARKETPLACE = 'swissup_argento_marketplace';
    const ARGENTO_PURE    = 'swissup_argento_pure';
    const ARGENTO_PURE2   = 'swissup_argento_pure2';
    const ARGENTO_STRIPES = 'swissup_argento_stripes';
    const ARGENTO_BREEZE_BUSINESS = 'swissup_argentobreeze_business';
    const ARGENTO_BREEZE_CHIC    = 'swissup_argentobreeze_chic';
    const ARGENTO_BREEZE_FORCE   = 'swissup_argentobreeze_force';
    const ARGENTO_BREEZE_STRIPES = 'swissup_argentobreeze_stripes';

    /** Modes */
    const MODE_CREATE_AND_SAVE = 'create_save';
    const MODE_UPDATE          = 'update';

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;
    /**
     * @var \Magento\MediaStorage\Model\File\Storage\File
     */
    protected $mediaStorage = null;
    /**
     * Config data loader
     *
     * @var \Magento\Config\Model\Config\Loader
     */
    protected $configLoader;

    /**
     * @var \Magento\Config\Model\Config\Structure
     */
    protected $configStructure;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;
    /**
     * @var \Swissup\ThemeEditor\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Framework\App\Cache\TypeListInterface
     */
    protected $cacheTypeList;

    /**
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Magento\MediaStorage\Model\File\Storage\FileFactory $mediaStorageFactory
     * @param \Magento\Config\Model\Config\Loader $configLoader
     * @param \Magento\Config\Model\Config\Structure $configStructure
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Swissup\ThemeEditor\Helper\Data $helper
     * @param \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList
     */
    public function __construct(
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\MediaStorage\Model\File\Storage\FileFactory $mediaStorageFactory,
        \Magento\Config\Model\Config\Loader $configLoader,
        \Magento\Config\Model\Config\Structure $configStructure,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Swissup\ThemeEditor\Helper\Data $helper,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList
    ) {
        $this->messageManager = $messageManager;
        $this->mediaStorage = $mediaStorageFactory->create();
        $this->configLoader = $configLoader;
        $this->configStructure = $configStructure;
        $this->storeManager = $storeManager;
        $this->helper = $helper;
        $this->cacheTypeList = $cacheTypeList;
    }

    /**
     * Get themes list
     *
     * @return array
     */
    public function getThemesList()
    {
        return [
            self::ABSOLUTE_THEME,
            self::ARGENTO_CHIC,
            self::ARGENTO_ESSENCE,
            self::ARGENTO_FLAT,
            self::ARGENTO_FORCE,
            self::ARGENTO_HOME,
            self::ARGENTO_LUXURY,
            self::ARGENTO_MALL,
            self::ARGENTO_MARKETPLACE,
            self::ARGENTO_PURE,
            self::ARGENTO_PURE2,
            self::ARGENTO_STRIPES,
            self::ARGENTO_BREEZE_BUSINESS,
            self::ARGENTO_BREEZE_CHIC,
            self::ARGENTO_BREEZE_FORCE,
            self::ARGENTO_BREEZE_STRIPES,
        ];
    }

    public function getStorage()
    {
        return $this->mediaStorage;
    }

    /**
     * @param string $theme
     * @param string $storeId
     * @param string $websiteId
     * @param string $mode
     * @return void
     */
    public function generateAndSave($theme, $storeId, $websiteId, $mode)
    {
        list($storeCode, $websiteCode) = $this->getCodesFromIds($storeId, $websiteId);
        $filePath = $this->getFilePath($theme, $storeCode, $websiteCode);
        if (self::MODE_UPDATE === $mode) {
            if (!file_exists($this->getStorage()->getMediaBaseDirectory() . '/' . $filePath)) {
                return;
            }
        }
        $config = $this->getThemeConfig($theme, $storeId, $websiteId);
        $css    = $this->convertConfigToCss($theme, $config);
        if (empty($css)) {
            $this->removeFile($theme, $storeId, $websiteId);
            return; // no need to save file when css is empty
        }

        try {
            $this->getStorage()->saveFile([
                'content'   => $css,
                // directory is required to throw exception properly.
                // @see Magento\MediaStorage\Model\File\Storage\File ~301
                'directory' => '',
                'filename'  => $filePath
            ], true);
            // Invalidate block_html and full_page cache to get new version of backend styles.
            $this->cacheTypeList->invalidate(\Magento\Framework\App\Cache\Type\Block::TYPE_IDENTIFIER);
            $this->cacheTypeList->invalidate(\Magento\PageCache\Model\Cache\Type::TYPE_IDENTIFIER);
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        }
    }

    /**
     * @param string $theme
     * @param string $storeId
     * @param string $websiteId
     * @return void
     */
    public function removeFile($theme, $storeId, $websiteId)
    {
        list($storeCode, $websiteCode) = $this->getCodesFromIds($storeId, $websiteId);
        $filePath = $this->getFilePath($theme, $storeCode, $websiteCode);
        $file = $this->getStorage()->getMediaBaseDirectory() . '/' . $filePath;
        if (file_exists($file)) {
            unlink($file);
            // Invalidate block_html and full_page cache to get new version of backend styles.
            $this->cacheTypeList->invalidate(\Magento\Framework\App\Cache\Type\Block::TYPE_IDENTIFIER);
            $this->cacheTypeList->invalidate(\Magento\PageCache\Model\Cache\Type::TYPE_IDENTIFIER);
        }
    }

    /**
     * Retrieve css filepath, relative to media folder
     *
     * @param string $theme
     * @param string $storeCode
     * @param string $websiteCode
     * @return string
     */
    public function getFilePath($theme, $storeCode, $websiteCode)
    {
        $suffix = '_backend.css';
        if ($storeCode) {
            $prefix = implode('_', [
                $websiteCode,
                $storeCode
            ]);
        } elseif ($websiteCode) {
            $prefix = $websiteCode;
        } else {
            $prefix = 'admin';
        }
        return str_replace('_', '/', $theme) . '/' . 'css' . '/' . $prefix . $suffix;
    }

    /**
     * @param string $theme
     * @param string $storeId
     * @param string $websiteId
     * @return array
     */
    public function getThemeConfig($theme, $storeId, $websiteId)
    {
        if ($storeId) {
            $scope     = ScopeInterface::SCOPE_STORES;
            $scopeCode = $storeId;
        } elseif ($websiteId) {
            $scope     = ScopeInterface::SCOPE_WEBSITES;
            $scopeCode = $websiteId;
        } else {
            $scope     = ScopeConfigInterface::SCOPE_TYPE_DEFAULT;
            $scopeCode = null;
        }

        $node = $this->configLoader->getConfigByPath($theme, $scope, $scopeCode);
        $defaultNode = [];
        $websiteNode = [];

        if ($storeId || $websiteId) {
            $defaultNode = $this->configLoader->getConfigByPath(
                $theme,
                ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
                null
            );
        }

        if ($storeId) {
            $websiteNode = $this->configLoader->getConfigByPath(
                $theme,
                ScopeInterface::SCOPE_WEBSITES,
                $this->storeManager->getStore($storeId)->getWebsite()->getId()
            );
        }

        $tab = $this->configStructure->getElement($theme);
        if (!$node || !$tab) {
            return [];
        }

        // Loop over actually available nodes in system.xml.
        // Not over $node values, taken from saved config.
        // This makes config to be written in correct order into css file.
        $config = [];
        foreach ($this->collectPaths($tab) as $path) {
            if (empty($node[$path]) &&
                empty($defaultNode[$path]) &&
                empty($websiteNode[$path])
            ) {
                continue;
            }

            $value = $this->helper->getScopeConfig()->getValue($path, $scope, $scopeCode);
            $parts = explode('/', $path);
            $valueId = $this->helper->camel2dashed(array_pop($parts));
            $groupId = array_pop($parts);
            $config[$groupId][$valueId] = $value;
        }

        return $config;
    }

    /**
     * Collect all paths from requested $section
     *
     * @param  Magento\Config\Model\Config\Structure\ElementInterface $section
     * @param  array  &$paths
     * @return array
     */
    protected function collectPaths($section, &$paths = [])
    {
        foreach ($section->getChildren() as $field) {
            if ($field instanceof \Magento\Config\Model\Config\Structure\Element\Field) {
                $paths[$field->getPath()] = $field->getPath();
            } else {
                $this->collectPaths($field, $paths);
            }
        }
        return $paths;
    }

    /**
     * @param String $theme
     * @param array $config
     * <pre>
     *  background
     *      body_background-color => #fff
     *      ...
     *  font
     *      body_font-family      => Helvetica,Arial,sans-serif
     *      page-header_color     => #000
     *      page-header_font-size => 12px
     *      ...
     *  style
     *      css => inline css
     *  css_selector
     *      body => body
     *      page-header => h1
     * </pre>
     */
    public function convertConfigToCss($theme, $config)
    {
        $groups = [];
        $groupsToSkip = [
            'css_selector',
            'head',
            'media_query',
            'homepage',
            'category',
            'product',
            'product_tabs',
            'product_tocart',
            'product_image',
            'header_config',
            'frontend_mode'
        ];
        $propsToSkip  = ['heading', 'head_link', 'sticky_header'];
        foreach ($config as $groupName => $groupValues) {
            if (in_array($groupName, $groupsToSkip)) {
                continue;
            }
            foreach ($groupValues as $name => $value) {
                $value = (string)$value;

                if (strpos($name, '_cssprop_') !== false) {
                    $name = str_replace('_cssprop_', '_--', $name);
                } elseif (strpos($name, '_cssproprgb_') !== false) {
                    $name = str_replace('_cssproprgb_', '_--', $name);
                    if (strlen($value) === 4) {
                        $value = implode(',', sscanf($value, "#%1x%1x%1x"));
                    } elseif (strlen($value) === 7) {
                        $value = implode(',', sscanf($value, "#%2x%2x%2x"));
                    }
                }

                list($key, $prop) = explode('_', $name);
                if (in_array($prop, $propsToSkip)) {
                    continue;
                }
                if ($method = $this->_getExtractorMethodName($prop)) {
                    $value = $this->$method($value);
                }
                if (false === $value || strlen($value) === 0) {
                    continue; // feature to keep default theme styles from theme.css
                }
                $groups[$groupName][$key][] = "{$prop}:{$value};";
            }
        }
        $css = '';
        foreach ($groups as $groupName => $groupedCss) {
            $tmpCss = '';
            foreach ($groupedCss as $key => $cssArray) {
                if (empty($config['css_selector'])
                    || !is_array($config['css_selector'])
                    || empty($config['css_selector'][$key])) {

                    $selector = $this->helper->getScopeConfig()
                        ->getValue($theme . '/css_selector/' . $key);
                    if (empty($selector)) {
                        continue;
                    }
                } else {
                    $selector = $config['css_selector'][$key];
                }

                $styles = implode('', $cssArray);
                $tmpCss .= "{$selector}{{$styles}}\n";
            }

            // wrap into @media query if needed
            $mediaQuery = $this->helper->getScopeConfig()
                ->getValue($theme . '/media_query/' . $groupName);
            if (!empty($mediaQuery)) {
                $tmpCss = "{$mediaQuery}{\n{$tmpCss}}\n";
            }

            $css .= $tmpCss;
        }

        if (!empty($config['head']['css'])) {
            $css .= $config['head']['css'];
        }
        return $css;
    }
    /**
     * @param string $property
     * @return string|false
     */
    protected function _getExtractorMethodName($property)
    {
        $property = str_replace('-', ' ', $property);
        $property = ucwords($property);
        $property = str_replace(' ', '', $property);
        $method = '_extract' . $property;
        if (method_exists($this, $method)) {
            return $method;
        }
        return false;
    }
    protected function _extractBackgroundImage($value)
    {
        // fix to prevent activating of 'Use default' checkbox, when image is deleted
        if (empty($value) || 'none' === $value) {
            $value = 'none';
        } elseif (strpos($value, 'data:image') === 0) {
            $value = 'url(' . $value . ');';
        } else {
            $value = 'url(../images/' . $value . ')';
        }
        return $value;
    }
    protected function _extractBackgroundColor($value)
    {
        if (empty($value)) {
            $value = 'transparent';
        }
        return $value;
    }
    protected function _extractBackgroundPosition($value)
    {
        return str_replace(',', ' ', $value);
    }

    /**
     * Get website and store codes from ids
     * @param  int $storeId
     * @param  int $websiteId
     * @return Array
     */
    private function getCodesFromIds($storeId, $websiteId)
    {
        $storeCode = null;
        $websiteCode = null;
        if ($storeId) {
            $storeCode = $this->storeManager->getStore($storeId)->getCode();
            $websiteCode = $this->storeManager->getStore($storeId)->getWebsite()->getCode();
        } else if ($websiteId) {
            $websiteCode = $this->storeManager->getWebsite($websiteId)->getCode();
        }

        return [$storeCode, $websiteCode];
    }
}
