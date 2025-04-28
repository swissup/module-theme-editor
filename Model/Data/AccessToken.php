<?php

namespace Swissup\ThemeEditor\Model\Data;

use Magento\Framework\Encryption\Helper\Security;
use Magento\Framework\Exception\LocalizedException;

class AccessToken implements \Swissup\ThemeEditor\Api\AccessTokenInterface
{
    const CACHE_ID = 'swissup_theme_editor_3h_access_token';
    const CACHE_TAG = 'swissup_theme_editor_client';
    const LIFETIME = 10800; // 3 hour
    const PARAM_NAME = 'theme_editor_access_token';

    /**
     * @var \Magento\Framework\Math\Random
     */
    private $mathRandom;

    /**
     * @var \Magento\Framework\Cache\FrontendInterface
     */
    private $cacheFrontend;

    /**
     * @param \Magento\Framework\Math\Random $mathRandom
     * @param \Magento\Framework\App\Cache\Frontend\Pool $cacheFrontend
     */
    public function __construct(
        \Magento\Framework\Math\Random $mathRandom,
        \Magento\Framework\App\Cache\Frontend\Pool $cacheFrontendPool
    ) {
        $this->mathRandom = $mathRandom;
        $this->cacheFrontend = $cacheFrontendPool->get(\Magento\Framework\App\Cache\Frontend\Pool::DEFAULT_FRONTEND_ID);
    }

    /**
     * Retrieve State Token
     *
     * @return string A 16 bit unique key
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getToken()
    {
        if (!$this->isPresent()) {
            $this->set($this->mathRandom->getRandomString(16));
        }
        return $this->cacheFrontend->load(self::CACHE_ID);
    }

    /**
     * Determine if the token is present in the 'session'
     *
     * @return bool
     */
    private function isPresent()
    {
        return (bool) $this->cacheFrontend->test(self::CACHE_ID);
    }

    /**
     * Save the value of the token
     *
     * @param string $value
     * @return void
     */
    private function set($value)
    {
        $this->cacheFrontend->save((string) $value, self::CACHE_ID, [self::CACHE_TAG], self::LIFETIME);
    }


    /**
     * Validate
     *
     * @param \Magento\Framework\App\RequestInterface $request
     * @return bool
     * @throws LocalizedException
     */
    public function validate($token)
    {
        return $token && Security::compareStrings($token, $this->getToken());
    }

    /**
     * @return string
     */
    public function getParamName():string
    {
        return self::PARAM_NAME;
    }

    /**
     * Validate request
     *
     * @param \Magento\Framework\App\RequestInterface $request
     * @return bool
     */
    public function validateRequest(\Magento\Framework\App\RequestInterface $request)
    {
        $token = $request->getParam($this->getParamName(), null);
        return $this->validate($token);
    }
}
