<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Shared\Testify;

// This is the only place where Project namespace is allowed
use Exception;
use Propel\Runtime\Propel;
use Pyz\Yves\Application\YvesBootstrap;
use Pyz\Zed\Application\Communication\ZedBootstrap;
use ReflectionObject;
use Silex\Application;
use Spryker\Shared\Kernel\LocatorLocatorInterface;
use Spryker\Shared\Library\Application\Environment;
use Spryker\Yves\Kernel\Locator;
use Spryker\Zed\Kernel\Locator as KernelLocator;
use Spryker\Zed\Propel\Communication\Plugin\ServiceProvider\PropelServiceProvider;

class SystemUnderTestBootstrap
{

    const APPLICATION_ZED = 'Zed';
    const APPLICATION_YVES = 'Yves';
    const APPLICATION_SHARED = 'Shared';
    const APPLICATION_CLIENT = 'Client';
    const TEST_ENVIRONMENT = 'test';

    /**
     * @var \Spryker\Shared\Testify\SystemUnderTestBootstrap
     */
    protected static $instance;

    /**
     * @var array
     */
    protected $applications = [
        self::APPLICATION_ZED,
        self::APPLICATION_YVES,
        self::APPLICATION_SHARED,
        self::APPLICATION_CLIENT,
    ];

    /**
     * @return $this
     */
    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @param string $application
     *
     * @return void
     */
    public function bootstrap($application = self::APPLICATION_ZED)
    {
        Propel::disableInstancePooling();

        $this->validateApplication($application);
        error_reporting(E_ALL | E_STRICT);
        ini_set('display_errors', 1);

        defined('APPLICATION') || define('APPLICATION', strtoupper($application));
        defined('APPLICATION_ENV') || define('APPLICATION_ENV', self::TEST_ENVIRONMENT);

        $path = realpath(__DIR__ . '/../../../../../../../../..');

        defined('APPLICATION_ROOT_DIR') || define('APPLICATION_ROOT_DIR', $path);

        Environment::initialize();

        if (self::APPLICATION_ZED === $application) {
            $this->bootstrapZed();
        }
        if (self::APPLICATION_YVES === $application) {
            $this->bootstrapYves();
        }
    }

    /**
     * @param string $application
     *
     * @throws \Exception
     *
     * @return void
     */
    protected function validateApplication($application)
    {
        if (!in_array($application, $this->applications)) {
            throw new Exception('Given application "' . $application . '" is not a valid application for running tests!');
        }
    }

    /**
     * @return void
     */
    protected function bootstrapZed()
    {
        $application = new ZedBootstrap();
        $locator = KernelLocator::getInstance();
        $this->resetLocator($locator);
        $application->boot();

        $propelServiceProvider = new PropelServiceProvider();
        $propelServiceProvider->boot(new Application());
    }

    /**
     * @TODO do we need to bootstrap Yves in a test case?
     *
     * @return void
     */
    protected function bootstrapYves()
    {
        $application = new YvesBootstrap();

        $locator = Locator::getInstance();
        $this->resetLocator($locator);

        $application->boot();
    }

    /**
     * @param \Spryker\Shared\Kernel\LocatorLocatorInterface $locator
     *
     * @return void
     */
    private function resetLocator(LocatorLocatorInterface $locator)
    {
        $refObject = new ReflectionObject($locator);
        $parent = $refObject->getParentClass();

        $refProperty = $parent->getProperty('instance');
        $refProperty->setAccessible(true);
        $refProperty->setValue(null);
    }

}
