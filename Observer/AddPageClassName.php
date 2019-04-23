<?php
namespace Swissup\ThemeEditor\Observer;

use Magento\Store\Model\ScopeInterface;
use Magento\Framework\Event\ObserverInterface;

class AddPageClassName implements ObserverInterface
{
    /**
     * @var \Magento\Framework\View\Page\Config
     */
    protected $pageConfig;

    /**
     * @var \Magento\Framework\View\ConfigInterface
     */
    protected $viewConfig;

    /**
     * @var \Swissup\ThemeEditor\Helper\Helper
     */
    protected $helper;

    /**
     * @param \Magento\Framework\View\Page\Config $pageConfig
     * @param \Magento\Framework\View\ConfigInterface $viewConfig
     * @param \Swissup\ThemeEditor\Helper\Helper $helper
     */
    public function __construct(
        \Magento\Framework\View\Page\Config $pageConfig,
        \Magento\Framework\View\ConfigInterface $viewConfig,
        \Swissup\ThemeEditor\Helper\Helper $helper
    ) {
        $this->pageConfig = $pageConfig;
        $this->viewConfig = $viewConfig;
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
        $action = $observer->getEvent()->getFullActionName();
        $themeCode = strtolower(str_replace(['/', '-'], '_', $theme->getCode()));

        $items = $this->viewConfig
            ->getViewConfig()
            ->getVarValue('Swissup_ThemeEditor', 'add_css_class');
        if (!is_array($items)) {
            $items = [];
        }

        foreach ($items as $item) {
            $value = true;

            if (isset($item['config'])) {
                $value = $this->helper->getScopeConfig()->getValue(
                    $item['config'],
                    ScopeInterface::SCOPE_STORE
                );
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
