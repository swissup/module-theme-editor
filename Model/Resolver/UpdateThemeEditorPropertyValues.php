<?php
namespace Swissup\ThemeEditor\Model\Resolver;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\GraphQl\Exception\GraphQlAuthorizationException;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\App\Emulation;

class UpdateThemeEditorPropertyValues implements ResolverInterface
{

    /**
     * Backend Config Model Factory
     *
     * @var \Magento\Config\Model\Config\Factory
     */
    private $configFactory;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var StoreManagerInterface;
     */
    private $storeManager;

    /**
     * @var \Swissup\ThemeEditor\Model\Data\AccessToken
     */
    private $accessToken;

    /**
     * @var \Swissup\ThemeEditor\Model\CssFactory
     */
    private $cssModel;

    /**
     * @var \Magento\Store\Api\Data\StoreInterface|null
     */
    private $store = null;

    /**
     * @param \Magento\Config\Model\Config\Factory $configFactory
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreManagerInterface $storeManager
     * @param \Swissup\ThemeEditor\Model\Data\AccessToken $accessToken
     * @param \Swissup\ThemeEditor\Model\Css $cssModel
     */
    public function __construct(
        \Magento\Config\Model\Config\Factory $configFactory,
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager,
        \Swissup\ThemeEditor\Model\Data\AccessToken $accessToken,
        \Swissup\ThemeEditor\Model\Css $cssModel
    ) {
        $this->configFactory = $configFactory;
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->accessToken = $accessToken;
        $this->cssModel = $cssModel;
    }

    /**
     * @inheritdoc
     */
    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        ?array $value = null,
        ?array $args = null
    ) {

        $input = $args['input'];
        $accessToken = $input['accessToken'] ?? null;
        if (empty($accessToken)) {
            throw new GraphQlInputException(__('Field "accessToken" required.' . $accessToken));
        }

        if (!$this->accessToken->validate($accessToken)) {
            throw new GraphQlInputException(__('"accessToken" is not valid.'));
        }

        $propsKey = 'props';
        if (!isset($input[$propsKey]) || !is_array($input[$propsKey])) {
            throw new GraphQlInputException(__('Field "propertyValues" must be an array.'));
        }

        $propertyValues = $input[$propsKey];
        $errors = [];

        foreach ($propertyValues as $propertyValue) {
            if (!isset($propertyValue['property']) || !is_string($propertyValue['property']) || empty($propertyValue['property'])) {
                $errors[] = __('The "path" field is required and must be a non-empty string.');
                continue;
            }
            if (!isset($propertyValue['value'])) {
                $errors[] = __('The "value" field is required for path "%1".', $propertyValue['property']);
                continue;
            }
            if (!$this->isConfigExists($propertyValue['property'])) {
                $errors[] = __('The "path" field is not correct for path "%1".', $propertyValue['property']);
                continue;
            }
            try {
                $this->saveConfig($propertyValue['property'], $propertyValue['value']);
            } catch (\Exception $e) {
                $errors[] = __('Could not save configuration for path "%1": %2', $propertyValue['property'], $e->getMessage());
            }
        }
        try {
            list($themeName) = explode('/', $propertyValue['property']);
            $this->regenerateCss($themeName);
        } catch (\Exception $e) {
            $errors[] = __('Could not regenerate css "%1": %2', $themeName, $e->getMessage());
        }

        if (!empty($errors)) {
            return [
                'success' => false,
                'message' => implode("\n", $errors),
            ];
        }

        return [
            'success' => true,
            'message' => __('Configuration values have been updated successfully.'),
        ];
    }

    private function isConfigExists($path)
    {
        $value = $this->scopeConfig->getValue($path);
        return $value !== null;
    }

    /**
     * Save configuration value for a given path.
     *
     * @param string $path Configuration path in the format 'section/group/field'.
     * @param mixed $value Value to be saved for the specified configuration path.
     * @return void
     */
    private function saveConfig($path, $value)
    {
        list($sectionName, $groupName, $fieldName) = explode('/', $path, 3);
//        $store = $this->getStore();
        $configData = [
            'section' => $sectionName,
//            'website' => $store->getWebsiteId(),
//            'store' => $store->getId(),
            'groups' => [
                $groupName => [
                    'fields' => [
                        $fieldName => [
                            'value' => $value
                        ]
                    ]
                ]
            ],
        ];

        /** @var \Magento\Config\Model\Config $configModel */
        $configModel = $this->configFactory->create(['data' => $configData]);
        $configModel->save();
    }

    private function regenerateCss($themeName)
    {
        $storeId = $websiteId = null;

        $css = $this->cssModel;
        $css->generateAndSave(
            $themeName,
            $storeId,
            $websiteId,
            \Swissup\ThemeEditor\Model\Css::MODE_CREATE_AND_SAVE
        );
    }

    /**
     * Retrieves the current store instance.
     *
     * If the store instance is not already set, it initializes it using the store manager.
     *
     * @return \Magento\Store\Api\Data\StoreInterface The current store instance.
     */
    private function getStore()
    {
        if ($this->store === null) {
            $this->store = $this->storeManager->getStore();//->getId();
        }
        return $this->store;
    }
}
