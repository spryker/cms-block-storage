<?php
/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace SprykerFeature\Yves\Setup;

use Silex\Application;
use Generated\Yves\Ide\FactoryAutoCompletion\Setup;
use SprykerEngine\Yves\Kernel\AbstractDependencyContainer;

/**
 * Class SetupDependencyContainer
 * @package SprykerFeature\Yves\Setup
 */
class SetupDependencyContainer extends AbstractDependencyContainer
{
    /**
     * @var Setup
     */
    protected $factory;

    /**
     * @param Application $app
     * @param bool $sslEnabled
     * @return Router\MonitoringRouter
     */
    public function createMonitoringRouter(Application $app, $sslEnabled = false)
    {
        return $this->getFactory()->createRouterMonitoringRouter(
            $this->getLocator(),
            $app,
            $sslEnabled
        );
    }
}
