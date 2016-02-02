<?php

/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace Spryker\Zed\Propel\Communication\Plugin\ServiceProvider;

use Propel\Runtime\Connection\ConnectionManagerSingle;
use Propel\Runtime\Propel;
use Propel\Runtime\ServiceContainer\StandardServiceContainer;
use Silex\Application;
use Silex\ServiceProviderInterface;
use Spryker\Shared\Config;
use Spryker\Shared\Propel\PropelConstants;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Spryker\Zed\Propel\Communication\PropelCommunicationFactory;
use Spryker\Zed\Propel\Business\PropelFacade;

/**
 * @method PropelCommunicationFactory getFactory()
 * @method PropelFacade getFacade()
 */
class PropelServiceProvider extends AbstractPlugin implements ServiceProviderInterface
{

    const BUNDLE = 'Propel';

    /**
     * @param Application $app
     *
     * @return void
     */
    public function register(Application $app)
    {
    }

    /**
     * @param Application $app
     *
     * @return void
     */
    public function boot(Application $app)
    {
        $manager = new ConnectionManagerSingle();
        $manager->setConfiguration($this->getPropelConfig());
        $manager->setName('zed');

        $serviceContainer = $this->getServiceContainer();
        $serviceContainer->setAdapterClass('zed', Config::get(PropelConstants::ZED_DB_ENGINE));
        $serviceContainer->setConnectionManager('zed', $manager);
        $serviceContainer->setDefaultDatasource('zed');

        $this->addLogger($serviceContainer);

        if (Config::get(PropelConstants::PROPEL_DEBUG) && $this->hasConnection()) {
            $connection = Propel::getConnection();
            $connection->useDebug(true);
        }
    }

    /**
     * @return \Propel\Runtime\ServiceContainer\StandardServiceContainer
     */
    protected function getServiceContainer()
    {
        $serviceContainer = Propel::getServiceContainer();

        return $serviceContainer;
    }

    /**
     * Allowed try/catch. If we have no database setup, getConnection throws an Exception
     * ServiceProvider is called more then once and after setup of database we can enable debug
     *
     * @return bool
     */
    private function hasConnection()
    {
        try {
            Propel::getConnection();

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * @throws \Exception
     *
     * @return mixed
     */
    private function getPropelConfig()
    {
        $propelConfig = Config::get(PropelConstants::PROPEL)['database']['connections']['default'];
        $propelConfig['user'] = Config::get(PropelConstants::ZED_DB_USERNAME);
        $propelConfig['password'] = Config::get(PropelConstants::ZED_DB_PASSWORD);
        $propelConfig['dsn'] = Config::get(PropelConstants::PROPEL)['database']['connections']['default']['dsn'];

        return $propelConfig;
    }

    /**
     * @param StandardServiceContainer $serviceContainer
     *
     * @throws \ErrorException
     *
     * @return void
     */
    private function addLogger(StandardServiceContainer $serviceContainer)
    {
        $loggerCollection = $this->getFactory()->createLogger();

        foreach ($loggerCollection as $logger) {
            $serviceContainer->setLogger($logger->getName(), $logger);
        }
    }

}
