<?php

/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace Spryker\Client\ZedRequest;

use Spryker\Client\Kernel\AbstractFactory;
use Spryker\Shared\Config;
use Spryker\Client\ZedRequest\Client\HttpClient;
use Spryker\Client\ZedRequest\Client\ZedClient;

class ZedRequestFactory extends AbstractFactory
{

    /**
     * @return \Spryker\Client\ZedRequest\Client\ZedClient
     */
    public function createClient()
    {
        return new ZedClient(
            $this->createHttpClient()
        );
    }

    /**
     * @return \Spryker\Client\ZedRequest\Client\HttpClient
     *
     * @todo remove Factory usage: https://spryker.atlassian.net/browse/CD-439
     */
    protected function createHttpClient()
    {
        $httpClient = new HttpClient(
            $this->getProvidedDependency(ZedRequestDependencyProvider::CLIENT_AUTH),
            $this->getConfig()->getZedRequestBaseUrl(),
            $this->getConfig()->getRawToken()
        );

        return $httpClient;
    }

    /**
     * @return ZedRequestConfig
     */
    protected function getConfig()
    {
        return new ZedRequestConfig(Config::getInstance());
    }

}
