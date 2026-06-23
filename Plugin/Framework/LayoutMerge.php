<?php

namespace Swissup\ThemeEditor\Plugin\Framework;

class LayoutMerge
{
    /**
     * Handle that builds the configurable header.
     *
     * @see \Swissup\ThemeEditor\view\frontend\layout\swissup_configurable_header.xml
     */
    const CONFIGURABLE_HEADER_HANDLE = 'swissup_configurable_header';

    /**
     * @var array
     */
    private $layoutLinkIds;

    /**
     * @var \Swissup\ThemeEditor\Helper\Data
     */
    private $helper;

    /**
     * @var \Swissup\ThemeEditor\Model\ResourceModel\Layout\Update
     */
    private $update;

    /**
     * @var \Swissup\ThemeEditor\Model\Layout\ConfigurableHeaderUpdate
     */
    private $configurableHeaderUpdate;

    /**
     * @param \Swissup\ThemeEditor\Helper\Data $helper
     * @param \Swissup\ThemeEditor\Model\ResourceModel\Layout\Update $update
     * @param \Swissup\ThemeEditor\Model\Layout\ConfigurableHeaderUpdate $configurableHeaderUpdate
     */
    public function __construct(
        \Swissup\ThemeEditor\Helper\Data $helper,
        \Swissup\ThemeEditor\Model\ResourceModel\Layout\Update $update,
        \Swissup\ThemeEditor\Model\Layout\ConfigurableHeaderUpdate $configurableHeaderUpdate
    ) {
        $this->helper = $helper;
        $this->update = $update;
        $this->configurableHeaderUpdate = $configurableHeaderUpdate;
    }

    /**
     * @param \Magento\Framework\View\Model\Layout\Merge $subject
     * @param string $result
     * @param string $handle
     * @return string
     */
    public function afterGetDbUpdateString(
        \Magento\Framework\View\Model\Layout\Merge $subject,
        $result,
        $handle
    ) {
        $layoutLinkIds = $this->getLayoutLinkIds();

        if ($layoutLinkIds) {
            $result .=  $this->update->getThemeEditorUpdatesByHandle(
                $handle,
                $subject->getTheme(),
                $subject->getScope(),
                $layoutLinkIds
            );
        }

        // Inject the configurable header wiring (moves, column classes and the
        // conditional removal of the stock header) as a real layout update
        // string. It is parsed by readerPool->interpret() before the isolated
        // reader context is created in 2.4.9, so - unlike the legacy
        // ScheduledStructure plugin - it survives into layout generation.
        // Isolation was added in commit:
        // https://github.com/magento/magento2/commit/61945814f4ffba41ee518cfe11b432a7c33e3612
        if ($handle === self::CONFIGURABLE_HEADER_HANDLE) {
            $result .= $this->configurableHeaderUpdate->getLayoutUpdateXml();
        }

        return $result;
    }

    private function getLayoutLinkIds()
    {
        if ($this->layoutLinkIds !== null) {
            return $this->layoutLinkIds;
        }

        $this->layoutLinkIds = [];

        $editor = $this->helper->getThemeEditorCode();
        if ($editor) {
            $config = $this->helper->getConfigValue($editor);

            if ($config) {
                $this->layoutLinkIds = $this->collectLayoutLinkIds($config);
            }
        }

        return $this->layoutLinkIds;
    }

    private function collectLayoutLinkIds($config)
    {
        $ids = [];

        foreach ($config as $key => $value) {
            if (is_array($value)) {
                $ids = array_merge($ids, $this->collectLayoutLinkIds($value));
            } else {
                if (!$value || strpos($key, '_link_id') === false) {
                    continue;
                }

                $ids[] = $value;
            }
        }

        return $ids;
    }
}
