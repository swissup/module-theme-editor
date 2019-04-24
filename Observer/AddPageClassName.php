<?php

namespace Swissup\ThemeEditor\Observer;

use Magento\Framework\Event\ObserverInterface;

class AddPageClassName implements ObserverInterface
{
    /**
     * @var \Magento\Framework\View\Page\Config
     */
    protected $pageConfig;

    /**
     * @var \Swissup\ThemeEditor\Helper\Data
     */
    protected $helper;

    /**
     * @param \Magento\Framework\View\Page\Config $pageConfig
     * @param \Swissup\ThemeEditor\Helper\Data $helper
     */
    public function __construct(
        \Magento\Framework\View\Page\Config $pageConfig,
        \Swissup\ThemeEditor\Helper\Data $helper
    ) {
        $this->pageConfig = $pageConfig;
        $this->helper = $helper;
    }

    /**
     * Add CSS class for body base on config value
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $items = $this->helper->getViewConfigValue('add_css_class');
        if (!is_array($items)) {
            return;
        }

        $action = $observer->getEvent()->getFullActionName();
        foreach ($items as $item) {
            if (!is_array($item)) {
                continue;
            }

            $value = true;

            if (isset($item['config'])) {
                $value = $this->helper->getConfigValue($item['config']);
            }

            if ($value
                && (empty($item['handle'])
                    || $item['handle'] === $action
                    || $item['handle'] === 'default')
            ) {
                $this->pageConfig->addBodyClass($item['class']);
            }
        }
    }
}
