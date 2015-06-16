<?php

namespace SprykerFeature\Zed\Application\Communication\Console\ApplicationCheckStep;

use SprykerEngine\Zed\Kernel\Communication\AbstractDependencyContainer;
use SprykerEngine\Zed\Kernel\Business\AbstractFacade;
use SprykerEngine\Zed\Kernel\Container;
use SprykerFeature\Zed\Application\Business\ApplicationFacade;
use SprykerFeature\Zed\Application\Communication\ApplicationDependencyContainer;
use SprykerEngine\Zed\Kernel\Communication\Factory;
use SprykerEngine\Zed\Kernel\Locator;
use Psr\Log\AbstractLogger;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;

abstract class AbstractApplicationCheckStep extends AbstractLogger implements LoggerAwareInterface, LoggerInterface
{

    use LoggerAwareTrait;

    /**
     * @var ApplicationDependencyContainer
     */
    protected $dependencyContainer;

    /**
     * Logs with an arbitrary level.
     *
     * @param mixed $level
     * @param string $message
     * @param array $context
     *
     * @return null
     */
    public function log($level, $message, array $context = array())
    {
        if ($this->logger) {
            $this->logger->log($level, $message, $context);
        }
    }

    /**
     * @param AbstractDependencyContainer $dependencyContainer
     */
    public function setDependencyContainer(AbstractDependencyContainer $dependencyContainer)
    {
        $this->dependencyContainer = $dependencyContainer;
    }

    /**
     * @param Container $container
     */
    public function setExternalDependencies(Container $container)
    {
        $dependencyContainer = $this->getDependencyContainer();
        if (isset($dependencyContainer)) {
            $this->getDependencyContainer()->setContainer($container);
        }
    }

    /**
     * @return AbstractDependencyContainer
     */
    protected function getDependencyContainer()
    {
        return $this->dependencyContainer;
    }

    /**
     * @param AbstractFacade $facade
     */
    public function setFacade(AbstractFacade $facade)
    {
        $this->facade = $facade;
    }

    /**
     * @return AbstractFacade
     */
    protected function getFacade()
    {
        return $this->facade;
    }

    abstract public function run();
}
