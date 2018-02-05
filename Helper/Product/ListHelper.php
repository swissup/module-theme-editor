<?php
namespace Swissup\ThemeEditor\Helper\Product;

use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Helper\AbstractHelper;

class ListHelper extends AbstractHelper
{
    protected $placeForAddToCart = null;
    protected $listBlock = null;

    protected function getArgentoConfig($key)
    {
        $themeCode = $this->listBlock->getLayout()->getUpdate()->getTheme()->getCode();
        $theme = strtolower(str_replace(['/', '-'], '_', $themeCode));
        $path = [
            $theme . '/' . $key,
            'swissup_argento/' . $key,
        ];
        $value = $this->scopeConfig
            ->getValue(reset($path), ScopeInterface::SCOPE_STORE);

        if ($value === null) {
            $value = $this->scopeConfig
            ->getValue(next($path), ScopeInterface::SCOPE_STORE);
        }
        return $value;
    }

    public function getPlaceForAddToCart($mode = 'grid')
    {
        if ($this->placeForAddToCart === null) {
            $this->placeForAddToCart = $this->getArgentoConfig(
                'product_list/' . $mode . '/add_to_cart'
            );
        }
        return $this->placeForAddToCart;
    }

    public function initActionsBlock($listBlock)
    {
        $this->listBlock = $listBlock;
        $actionsBlock = $listBlock->getLayout()
            ->createBlock('Magento\Framework\View\Element\Template')
            ->setTemplate('Magento_Catalog::product/list/actions.phtml');
        $listBlock->setChild('actions', $actionsBlock);
    }
}
