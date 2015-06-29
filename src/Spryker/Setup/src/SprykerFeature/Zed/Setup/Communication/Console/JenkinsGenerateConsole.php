<?php
/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace SprykerFeature\Zed\Setup\Communication\Console;

use SprykerEngine\Zed\Transfer\Communication\Console\GeneratorConsole;
use SprykerFeature\Zed\Console\Business\Model\Console;
use SprykerFeature\Zed\Setup\Business\SetupFacade;
use SprykerFeature\Zed\Setup\Communication\Console\Npm\RunnerConsole;
use SprykerFeature\Zed\Installer\Communication\Console\InitializeDatabaseConsole;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

/**
 * @method SetupFacade getFacade()
 */
class JenkinsGenerateConsole extends Console
{

    const COMMAND_NAME = 'setup:jenkins:generate';
    const DESCRIPTION = 'Generate jenkins jobs configuration';

    protected function configure()
    {
        $this->setName(self::COMMAND_NAME);
        $this->setDescription(self::DESCRIPTION);
        $this->addOption(
            'role',
            'r',
            InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY,
            'Job roles to include on this host',
            []
        );

        parent::configure();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $result = $this->getFacade()->generateCronjobs(
            $input->getOption('role')
        );

        $output->writeln($result);
    }
}
