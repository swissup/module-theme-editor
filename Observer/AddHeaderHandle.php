<?php
namespace Swissup\ThemeEditor\Observer;

class AddHeaderHandle implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Swissup\ThemeEditor\Helper\Data
     */
    private $helper;

    /**
     * @param \Swissup\ThemeEditor\Helper\Data $helper
     */
    public function __construct(
        \Swissup\ThemeEditor\Helper\Data $helper
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
