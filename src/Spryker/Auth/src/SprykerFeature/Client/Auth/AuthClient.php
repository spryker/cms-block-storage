<?php

/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace SprykerFeature\Client\Auth;

use SprykerEngine\Client\Kernel\AbstractClient;
use SprykerFeature\Client\Auth\Token\TokenService;

/**
 * @method AuthDependencyContainer getDependencyContainer()
 */
class AuthClient extends AbstractClient implements AuthClientInterface
{

    /**
     * @param string $rawToken
     *
     * @return string
     */
    public function generateToken($rawToken)
    {
        return $this->getTokenService()->generate($rawToken);
    }

    /**
     * @param string $rawToken
     * @param string $hash
     *
     * @return bool
     */
    public function checkToken($rawToken, $hash)
    {
        return $this->getTokenService()->check($rawToken, $hash);
    }

    /**
     * @return TokenService
     */
    private function getTokenService()
    {
        return $this->getDependencyContainer()->createTokenService();
    }

}
