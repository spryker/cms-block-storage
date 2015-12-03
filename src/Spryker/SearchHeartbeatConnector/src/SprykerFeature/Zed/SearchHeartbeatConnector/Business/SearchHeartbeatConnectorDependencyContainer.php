<?php

/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace SprykerFeature\Zed\SearchHeartbeatConnector\Business;

use SprykerFeature\Zed\SearchHeartbeatConnector\Business\Assistant\SearchHealthIndicator;
use Elastica\Client;
use Generated\Zed\Ide\FactoryAutoCompletion\SearchHeartbeatConnectorBusiness;
use SprykerEngine\Zed\Kernel\Business\AbstractBusinessDependencyContainer;
use SprykerFeature\Shared\Heartbeat\Code\HealthIndicatorInterface;
use SprykerFeature\Shared\Library\Config;
use SprykerFeature\Shared\System\SystemConfig;

/**
 * @method SearchHeartbeatConnectorBusiness getFactory()
 */
class SearchHeartbeatConnectorDependencyContainer extends AbstractBusinessDependencyContainer
{

    /**
     * @return HealthIndicatorInterface
     */
    public function createHealthIndicator()
    {
        $client = new Client([
            'protocol' => Config::get(SystemConfig::ELASTICA_PARAMETER__TRANSPORT),
            'port' => Config::get(SystemConfig::ELASTICA_PARAMETER__PORT),
            'host' => Config::get(SystemConfig::ELASTICA_PARAMETER__HOST),
        ]);

        return new SearchHealthIndicator($client);
    }

}
