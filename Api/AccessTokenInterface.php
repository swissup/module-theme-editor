<?php
namespace Swissup\ThemeEditor\Api;

interface AccessTokenInterface
{
    public function getToken();

    public function validate($token);

    public function getParamName():string;

    public function validateRequest(\Magento\Framework\App\RequestInterface $request);
}
