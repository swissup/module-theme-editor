<?php
namespace Swissup\ThemeEditor\Controller\Header;

use Magento\Framework\Controller\ResultFactory;

class Preview extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Swissup\ThemeEditor\Helper\Header
     */
    protected $helper;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Swissup\ThemeEditor\Helper\Header $helper
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Swissup\ThemeEditor\Helper\Header $helper
    ) {
        $this->helper = $helper;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\View\Result\Page|void
     */
    public function execute()
    {
        $hash = $this->getRequest()->getParam('hash');
        if (!isset($hash) || !$this->helper->validatePreviewHash($hash)) {
            return $this->resultFactory
                ->create(ResultFactory::TYPE_FORWARD)->forward('noroute');
        }

        $this->_view->loadLayout();
        $this->_view->renderLayout();
    }
}
