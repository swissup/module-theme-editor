<?php
namespace Swissup\ThemeEditor\Observer;

use Magento\Store\Model\ScopeInterface;
use Magento\Framework\Event\ObserverInterface;

class AddPageClassName implements ObserverInterface
{
    /**
     * @var \Swissup\ThemeEditor\Helper\Helper
     */
    protected $helper;

    /**
     * @param \Magento\Framework\View\Page\Config $pageConfig
     * @param \Swissup\ThemeEditor\Helper\Helper $helper
     */
    public function __construct(
        \Magento\Framework\View\Page\Config $pageConfig,
        \Swissup\ThemeEditor\Helper\Helper $helper
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
        $theme = $observer->getLayout()->getUpdate()->getTheme();
        $themeCode = strtolower(str_replace(['/', '-'], '_', $theme->getCode()));
        $action = $observer->getEvent()->getFullActionName();
        $mapping = $this->helper->getScopeConfig()->getValue(
            $themeCode . '/add_css_class'
        );
        if (is_array($mapping)) {
            foreach ($mapping as $item) {
                $configValue = $this->helper->getScopeConfig()->getValue(
                    $item['related_config'],
                    ScopeInterface::SCOPE_STORE
                );

                if ($configValue
                    && ($item['action'] == $action || $item['action'] = 'default')
                ) {
                    $this->pageConfig->addBodyClass($item['css_class']);
                }
            }
        }
    }
}
