<?php

namespace Swissup\ThemeEditor\Model\ResourceModel\Layout;

class Update extends \Magento\Widget\Model\ResourceModel\Layout\Update
{
    /**
     * @var array
     */
    private $memo;

    /**
     * @param string $handle
     * @param \Magento\Framework\View\Design\ThemeInterface $theme
     * @param \Magento\Framework\App\ScopeInterface $store
     * @param array $store
     *
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getThemeEditorUpdatesByHandle(
        $handle,
        \Magento\Framework\View\Design\ThemeInterface $theme,
        \Magento\Framework\App\ScopeInterface $store,
        $layoutLinkIds
    ) {
        $cacheKey = implode('-', $layoutLinkIds);

        if (!isset($this->memo[$cacheKey])) {
            $this->memo[$cacheKey] = [];
            foreach ($this->fetchThemeEditorUpdatesByHandle($layoutLinkIds) as $layout) {
                // skip updates applied by magento logic (argento_eesence editor for argento_essence theme for example)
                if ($layout['theme_id'] == $theme->getId() && $layout['store_id'] == $store->getId()) {
                    continue;
                }

                if (!isset($this->memo[$cacheKey][$layout['handle']])) {
                    $this->memo[$cacheKey][$layout['handle']] = '';
                }

                $this->memo[$cacheKey][$layout['handle']] .= $layout['xml'];
            }
        }

        return $this->memo[$cacheKey][$handle] ?? '';
    }

    /**
     * @param array $layoutLinkIds
     *
     * @return \Magento\Framework\DB\Select
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function fetchThemeEditorUpdatesByHandle($layoutLinkIds)
    {
        $select = $this->getConnection()->select()
            ->from(
                ['layout_update' => $this->getMainTable()],
                ['xml', 'handle']
            )
            ->join(
                ['link' => $this->getTable('layout_link')],
                'link.layout_update_id=layout_update.layout_update_id',
                ['store_id', 'theme_id']
            )
            ->where('link.layout_link_id IN (?)', $layoutLinkIds)
            ->order('layout_update.sort_order ASC');

        return $this->getConnection()->fetchAll($select);
    }
}
