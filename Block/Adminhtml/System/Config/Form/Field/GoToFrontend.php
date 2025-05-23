<?php
namespace Swissup\ThemeEditor\Block\Adminhtml\System\Config\Form\Field;

/**
 * Class
 */
class GoToFrontend extends \Magento\Config\Block\System\Config\Form\Field
{
    private $urlBuilder;
    private $storeManager;
    private $accessToken;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Swissup\ThemeEditor\Model\Data\AccessToken $accessToken,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->urlBuilder = $urlBuilder;
        $this->storeManager = $storeManager;
        $this->accessToken = $accessToken;
    }

    /**
     * Retrieve element HTML markup
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
//        $websiteId = (int) $this->getRequest()->getParam('website');
        $storeId = $this->getRequest()->getParam('store');
        $store = $this->storeManager->getStore($storeId);

        $baseUrl = $this->urlBuilder->getBaseUrl([
//            '_scope' => \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
//            '_scope_id' => $storeId
        ], \Magento\Framework\UrlInterface::URL_TYPE_WEB);

        $params = [
            $this->accessToken->getParamName() => $this->accessToken->getToken(),
        ];
        $url = $baseUrl . '?' . http_build_query($params);

        $html = '<a href="' . $url . '" target="_blank">' . $url . '</a>';

        return $html;
//
//        /** @var \Magento\Backend\Block\Widget\Button $buttonBlock  */
//        $buttonBlock = $this->getForm()->getLayout()->createBlock(\Magento\Backend\Block\Widget\Button::class);
//        $data = [
//            'id' => 'go to',
//            'label' => __('Go to frontend'),
//            'onclick' => "setLocation('" . $url . "')",
//        ];
//
//        $html = $buttonBlock->setData($data)->toHtml();
//        return $html;
    }
}
