<?php

/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace SprykerFeature\Zed\SessionHeartbeatConnector\Business;

use SprykerFeature\Zed\SessionHeartbeatConnector\Business\Assistant\SessionHealthIndicator;
use Generated\Zed\Ide\FactoryAutoCompletion\SessionHeartbeatConnectorBusiness;
use SprykerEngine\Zed\Kernel\Business\AbstractBusinessDependencyContainer;
use SprykerFeature\Shared\Heartbeat\Code\HealthIndicatorInterface;

/**
 * @method SessionHeartbeatConnectorBusiness getFactory()
 */
class SessionHeartbeatConnectorDependencyContainer extends AbstractBusinessDependencyContainer
{

    /**
     * @return HealthIndicatorInterface
     */
    public function createHealthIndicator()
    {
        return new SessionHealthIndicator();
    }

}
