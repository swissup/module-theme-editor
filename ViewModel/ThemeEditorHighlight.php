<?php
namespace Swissup\ThemeEditor\ViewModel;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\Serialize\Serializer\Json;

class ThemeEditorHighlight implements ArgumentInterface
{
    const SESSION_TOKEN_ID_KEY = 'swissup_theme_editor_access_token_id';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var Json
     */
    private $jsonSerializer;

    /**
     * @var \Magento\Framework\Session\SessionManagerInterface
     */
    private $session;
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    private $request;
    /**
     * @var \Swissup\ThemeEditor\Model\Data\AccessToken
     */
    private $accessToken;

    /**
     * @var array|string|string[]
     */
    private $configSectionName;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    private $urlBuilder;

    /**
     * @var \Swissup\ThemeEditor\Model\Css
     */
    private $cssModel;


    public function __construct(
        ScopeConfigInterface $scopeConfig,
        Json $jsonSerializer,
        \Magento\Framework\Session\SessionManagerInterface $session,
        \Magento\Framework\App\RequestInterface $request,
        \Swissup\ThemeEditor\Model\Data\AccessToken $accessToken,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Framework\View\DesignInterface $design,
        \Swissup\ThemeEditor\Model\Css $cssModel
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->jsonSerializer = $jsonSerializer;
        $this->session = $session;
        $this->request = $request;
        $this->accessToken = $accessToken;
        $this->storeManager = $storeManager;
        $this->urlBuilder = $urlBuilder;
        $this->cssModel = $cssModel;

        $theme = $design->getDesignTheme();
        $this->configSectionName = $this->getConfigSectionNameByTheme($theme);
    }

    private function getConfigSectionNameByTheme($theme)
    {
        $themeCode = $theme->getCode();
        $themeCode = strtolower($themeCode);
        $themeCode = str_replace(['/', '-'], '_', $themeCode);
        $supportedThemeList = $this->cssModel->getThemesList();
        if (in_array($themeCode, $supportedThemeList)) {
            return $themeCode;
        }
        $parentTheme = $theme->getParentTheme();
        if ($parentTheme !== null) {
            return $this->getConfigSectionNameByTheme($parentTheme);
        }
        return $themeCode;
    }

    private function getAccessToken()
    {
        $request = $this->request;
        $paramName = $this->accessToken->getParamName();
        $sessionIdKey = self::SESSION_TOKEN_ID_KEY;
        $accessToken = $request->getParam(
            $paramName,
            $this->session->getData($sessionIdKey)
        );
        return $accessToken;
    }

    private function isLoggedIn()
    {
        $sessionIdKey = self::SESSION_TOKEN_ID_KEY;
        $accessToken = $this->getAccessToken();
        if (!empty($accessToken)) {
            $sessionValue = $this->accessToken->validate($accessToken) ? $accessToken : null;
            $this->session->setData($sessionIdKey, $sessionValue);
        }

        return (bool) $this->session->getData($sessionIdKey);
    }

    public function isEnabled()
    {
//        return true;
        $section = $this->configSectionName;
        $configPath = $section . '/frontend_mode/enabled';
        $isEnabled = $this->scopeConfig->isSetFlag($configPath, ScopeInterface::SCOPE_STORE);

        return $isEnabled && $this->isLoggedIn();
    }

    private function getGraphqlUrl()
    {
        return $this->urlBuilder->getUrl(
            'graphql',
            ['_secure' => $this->request->isSecure()]
        );
    }

    public function getSettings()
    {
        return $this->jsonSerializer->serialize([
            'configSectionName' => $this->configSectionName,
            'accessToken' => $this->getAccessToken(),
            'storeViewCode' => $this->storeManager->getStore()->getCode(),
            'graphqlUrl' => $this->getGraphqlUrl(),
        ]);
    }


}
