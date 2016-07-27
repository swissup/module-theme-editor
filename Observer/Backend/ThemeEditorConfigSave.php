<?php
namespace Swissup\ThemeEditor\Observer\Backend;

use Swissup\ThemeEditor\Model\Css as CssModel;

class ThemeEditorConfigSave implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Swissup\ThemeEditor\Model\CssFactory
     */
    private $cssModelFactory;
    /**
     * @var \Magento\Store\Model\ResourceModel\Store\CollectionFactory
     */
    protected $storesFactory;
    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $coreResource;

    /**
     * @param \Swissup\ThemeEditor\Model\CssFactory $cssModelFactory
     * @param \Magento\Store\Model\ResourceModel\Store\CollectionFactory $storesFactory
     * @param \Magento\Framework\App\ResourceConnection $coreResource
     */
    public function __construct(
        \Swissup\ThemeEditor\Model\CssFactory $cssModelFactory,
        \Magento\Store\Model\ResourceModel\Store\CollectionFactory $storesFactory,
        \Magento\Framework\App\ResourceConnection $coreResource
    ) {
        $this->cssModelFactory = $cssModelFactory;
        $this->storesFactory = $storesFactory;
        $this->coreResource = $coreResource;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $request = $observer->getControllerAction()->getRequest();
        $section = $request->getParam('section');
        $css = $this->cssModelFactory->create();
        $themes = $css->getThemesList();

        if (!in_array($section, $themes)) {
            return;
        }

        $storeId = $request->getParam('store');
        $websiteId = $request->getParam('website');

        // if all options are inherited - delete file
        if ($this->isEverythingInherited($request->getParam('groups'))) {
            $css->removeFile($section, $storeId, $websiteId);
        } else {
            $css->generateAndSave($section, $storeId, $websiteId, CssModel::MODE_CREATE_AND_SAVE);
        }
        if (!$storeId) {
            $descending = $this->getDescendingWebsiteAndStoreCodes($websiteId);
            foreach ($descending as $_websiteCode => $_storeCodes) {
                $css->generateAndSave($section, null, $_websiteCode, CssModel::MODE_UPDATE);
                foreach ($_storeCodes as $_storeCode) {
                    $css->generateAndSave($section, $_storeCode, $_websiteCode, CssModel::MODE_UPDATE);
                }
            }
        }
    }

    /**
     * Check each option for `inherit` flag.
     * Breaks on the first non-inherited option and returns false.
     *
     * @param  array  $group
     * @return boolean
     */
    protected function isEverythingInherited($group)
    {
        foreach ($group as $groupName => $groupValue) {
            if (isset($groupValue['fields'])) {
                foreach ($groupValue['fields'] as $fieldName => $fieldValue) {
                    if (empty($fieldValue['inherit'])) {
                        return false;
                    }
                }
            }
            if (isset($groupValue['groups'])) {
                if (!$this->isEverythingInherited($groupValue['groups'])) {
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * Retrieve pairs of store and website ids that are the childs
     * of given websiteId.
     * If websiteId is null, all stores and websites will be returned.
     *
     * Used to update all descending stores that already has css file,
     * to fix all inherited rules from currently saved store
     *
     * @param int $websiteId
     * @return array
     */
    protected function getDescendingWebsiteAndStoreCodes($websiteId)
    {
        $collection = $this->storesFactory->create()
            ->setWithoutDefaultFilter()
            ->join(
                ['website' => $this->coreResource->getTableName('store_website')],
                'website.website_id = main_table.website_id',
                ['website_code' => 'code']
            );
        if ($websiteId) {
            $collection->addFieldToFilter('website.website_id', $websiteId);
        }
        $result = [];
        foreach ($collection as $store) {
            $result[$store->getWebsiteId()][] = $store->getId();
        }
        return $result;
    }
}
