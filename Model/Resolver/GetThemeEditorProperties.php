<?php
namespace Swissup\ThemeEditor\Model\Resolver;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Swissup\ThemeEditor\ViewModel\ThemeEditorHighlight;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\Session\SessionManagerInterface;
use Swissup\ThemeEditor\Model\Data\AccessToken as AccessTokenModel;

class GetThemeEditorProperties implements ResolverInterface
{
    private $scopeConfig;

    private $configStructure;

    private $session;

    private $accessTokenModel;

    /**
     * @var array|string|string[]
     */
    private $configSectionName;

    private $selectors = false;

    private $groupLabels = [];

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Config\Model\Config\Structure $configStructure,
        SessionManagerInterface $session,
        AccessTokenModel $accessTokenModel
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->configStructure = $configStructure;
        $this->session = $session;
        $this->accessTokenModel = $accessTokenModel;
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
        $accessToken = $args['accessToken'] ?? null;
        if (!$this->isLoggedIn($accessToken)) {
            return [];
        }

        if (!isset($args['themeCode'])) {
            throw new \InvalidArgumentException(__('Required argument "themeCode" was not provided.'));
        }
        $themeCode = $args['themeCode'];
        $this->configSectionName = $themeCode;
        return $this->getProporties($this->configSectionName);
    }

    private function isLoggedIn($accessToken = null)
    {
        $sessionIdKey = ThemeEditorHighlight::SESSION_TOKEN_ID_KEY;
        $sessionValue = $this->session->getData($sessionIdKey);

        if ($accessToken) {
            return $this->accessTokenModel->validate($accessToken);
        }

        return (bool) $sessionValue;
    }

    private function getProporties($configSectionName = null)
    {
        $section = $configSectionName;
        $section = $this->configStructure->getElement($section);
        $data = $section->getData();
        $result = [];
        $this->extractDataRecursively($data, $result);

        return $result;
    }

    private function extractDataRecursively(array $data, array &$result = []) {
        $disallowedFrontMode = [
            'Swissup\ThemeEditor\Block\Adminhtml\System\Config\Form\Field\CssSelectorHeading'
        ];
        $selectors = $this->getSelectorsArray();
        $allowedSelectors = array_keys($selectors);

        $headingLabel = '';

        foreach ($data as $key => $value) {
            // Extract the desired values if they exist.
            if (isset($value['label'])) {
                $id = $value['id'] ?? ''; // Use null coalescing to handle missing 'id'
                $frontModel = $value['frontend_model'] ?? '';

                $cssConfigId = $id;
                $cssProperty = '';

                if ($value['_elementType'] == 'group') {
                    $this->groupLabels[$value['path'] . '/' . $value['id']] = $value['label'];
                }

                if ($frontModel == 'Swissup\ThemeEditor\Block\Adminhtml\System\Config\Form\Field\CssSelectorHeading') {
                    $headingLabel = $value['label'];
                }

                if (strpos($id, '_') !== false
                    && !in_array($frontModel, $disallowedFrontMode)
//                    && !isset($value['backend_model'])
//                    && !isset($value['children'])
                ) {
                    list($cssConfigId, $cssProperty) = explode('_', $id, 2);
                    $cssConfigId = $this->camelToKebabCase($cssConfigId);

                    if (in_array($cssConfigId, $allowedSelectors)) {
                        if (!isset($result[$cssConfigId])) {
                            $result[$cssConfigId] = [
                                'key' => $cssConfigId,
                                'selector' => $selectors[$cssConfigId] ?? '',
                                'label' => $headingLabel, //. ' / ' . $value['label'],
                                'properties' => [],
                                'groupLabel' => $this->groupLabels[$value['path']] ?? '',
                            ];
                        }
                        $configPath = $value['path'] . '/'. $key;
                        $configValue = (string) $this->scopeConfig->getValue($configPath, ScopeInterface::SCOPE_STORE);
                        $result[$cssConfigId]['properties'][] = [
                            'id'    => $id,
                            'label' => $value['label'],
                            'path' => $configPath,
                            'selector' => $selectors[$cssConfigId] ?? '',
                            'property' => $this->camelToKebabCase($cssProperty),
                            'value' => $configValue,
//                            '_origin' => $value
                        ];
                    }
                }
            }

            // Recurse into 'children' arrays.
            if (is_array($value) && isset($value['children'])) {
                $this->extractDataRecursively($value['children'], $result);
            } elseif (is_array($value)) {
                 $this->extractDataRecursively($value, $result);
            }
        }
        return $result;
    }

    private function getSelectorsArray()
    {
        if ($this->selectors === false) {
            $selectors = [];
            $section = $this->configSectionName;
            $configPath = $section . '/css_selector';
            $groups = $this->scopeConfig->getValue($configPath, ScopeInterface::SCOPE_STORE);

            if (is_array($groups)) {
                foreach ($groups as $key => $selector) {
                    if ($selector) {
                        $selectors[$key] = $selector;
                    }
                }
            }
            $this->selectors = $selectors;
        }

        return $this->selectors;
    }

    private function camelToKebabCase(string $input): string {
        // Use preg_replace to find uppercase letters and replace them with a dash and the lowercase version of the letter.
        return strtolower(preg_replace('/([A-Z])/', '-$1', $input));
    }
}
