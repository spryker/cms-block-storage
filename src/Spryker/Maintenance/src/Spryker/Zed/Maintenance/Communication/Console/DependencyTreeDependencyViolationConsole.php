<?php

/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace Spryker\Zed\Maintenance\Communication\Console;

use Spryker\Zed\Console\Business\Model\Console;
use Spryker\Zed\Maintenance\Business\MaintenanceFacade;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @method MaintenanceFacade getFacade()
 */
class DependencyTreeDependencyViolationConsole extends Console
{

    const COMMAND_NAME = 'code:dependency-violation-finder';

    /**
     * @return void
     */
    protected function configure()
    {
        parent::configure();

        $this
            ->setName(self::COMMAND_NAME)
            ->setHelp('<info>' . self::COMMAND_NAME . ' -h</info>')
            ->setDescription('Find dependency violations in the dependency tree');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @throws \Exception
     *
     * @return void
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->info('Find dependency violations');

        $dependencyViolations = $this->getFacade()->getDependencyViolations();

        $this->info(sprintf('Found "%d" wrong dependencies', count($dependencyViolations)));

        foreach ($dependencyViolations as $dependencyViolation) {
            $this->info($dependencyViolation);
        }
    }

}
