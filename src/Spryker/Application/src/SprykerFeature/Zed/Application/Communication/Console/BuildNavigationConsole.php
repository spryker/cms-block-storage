<?php

/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace SprykerFeature\Zed\Application\Communication\Console;

use SprykerFeature\Shared\Library\Application\Environment;
use SprykerFeature\Zed\Application\Business\ApplicationFacade;
use SprykerFeature\Zed\Application\Communication\Console\ApplicationCheckStep\AbstractApplicationCheckStep;
use SprykerFeature\Zed\Console\Business\Model\Console;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @method ApplicationFacade getFacade()
 */
class BuildNavigationConsole extends Console
{

    const COMMAND_NAME = 'application:build-navigation';
    const DESCRIPTION = 'Build the navigation tree';

    protected function configure()
    {
        $this->setName(self::COMMAND_NAME);
        $this->setDescription(self::DESCRIPTION);

        parent::configure();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->getFacade()->prepareNavigation();
    }

}
