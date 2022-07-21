<?php

namespace Swissup\ThemeEditor\Plugin\Framework;

class LayoutMerge
{
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
     * @param \Swissup\ThemeEditor\Helper\Data $helper
     * @param \Swissup\ThemeEditor\Model\ResourceModel\Layout\Update $update
     */
    public function __construct(
        \Swissup\ThemeEditor\Helper\Data $helper,
        \Swissup\ThemeEditor\Model\ResourceModel\Layout\Update $update
    ) {
        $this->helper = $helper;
        $this->update = $update;
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
