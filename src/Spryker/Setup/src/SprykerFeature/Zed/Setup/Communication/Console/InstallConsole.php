<?php

namespace SprykerFeature\Zed\Setup\Communication\Console;

use SprykerFeature\Zed\Maintenance\Communication\Console\FossMarkDownGeneratorConsole;
use SprykerEngine\Zed\Transfer\Communication\Console\GeneratorConsole;
use SprykerFeature\Zed\Console\Business\Model\Console;
use SprykerFeature\Zed\Setup\Business\Model\GeneratedDirectoryRemover;
use SprykerFeature\Zed\Setup\Communication\Console\Npm\RunnerConsole;
use SprykerFeature\Zed\Installer\Communication\Console\InitializeDatabaseConsole;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\Process\Process;


class InstallConsole extends Console
{

    const COMMAND_NAME = 'setup:install';
    const DESCRIPTION = 'Setup the application';

    protected function configure()
    {
        $this->setName(self::COMMAND_NAME);
        $this->setDescription(self::DESCRIPTION);

        parent::configure();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->runDependingCommand(RemoveGeneratedDirectoryConsole::COMMAND_NAME);
        $this->runDependingCommand(PropelConsole::COMMAND_NAME);
        $this->runDependingCommand(GeneratorConsole::COMMAND_NAME);
        $this->runDependingCommand(InitializeDatabaseConsole::COMMAND_NAME);
        $this->runDependingCommand(GenerateIdeAutoCompletionConsole::COMMAND_NAME);
        $this->runDependingCommand(RunnerConsole::COMMAND_NAME, ['--' . RunnerConsole::OPTION_TASK_BUILD_ALL]);
        $this->runDependingCommand(FossMarkDownGeneratorConsole::COMMAND_NAME);
    }
}
