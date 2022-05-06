<?php
namespace Swissup\ThemeEditor\Observer;

class AddHeaderHandle implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Swissup\ThemeEditor\Helper\Header
     */
    private $helper;

    /**
     * @param \Swissup\ThemeEditor\Helper\Header $helper
     */
    public function __construct(
        \Swissup\ThemeEditor\Helper\Header $helper
    ) {
        $this->helper = $helper;
    }

    /**
     * Add configurable header layout update
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $layoutUpdate = $observer->getLayout()->getUpdate();
        $theme = $layoutUpdate->getTheme();

        if ($this->helper->isHeaderEnabled($theme)) {
            $layoutUpdate->addHandle('swissup_configurable_header');
        }
    }
}
