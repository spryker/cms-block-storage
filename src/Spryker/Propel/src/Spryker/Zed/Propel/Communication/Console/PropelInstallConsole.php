<?php

/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace Spryker\Zed\Propel\Communication\Console;

use Spryker\Zed\Console\Business\Model\Console;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class PropelInstallConsole extends Console
{

    const OPTION_NO_DIFF = 'no-diff';
    const OPTION_NO_DIFF_SHORTCUT = 'o';
    const OPTION_NO_DIFF_DESCRIPTION = 'Runs without propel:diff';

    const COMMAND_NAME = 'propel:install';
    const DESCRIPTION = 'Runs config convert, create database, postgres compatibility, copy schemas, runs Diff, build models and migrate tasks';

    /**
     * @return void
     */
    protected function configure()
    {
        $this->setName(self::COMMAND_NAME);
        $this->setDescription(self::DESCRIPTION);

        $this->addOption(
            self::OPTION_NO_DIFF,
            self::OPTION_NO_DIFF_SHORTCUT,
            InputOption::VALUE_NONE,
            self::OPTION_NO_DIFF_DESCRIPTION
        );

        parent::configure();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $dependingCommands = $this->getDependingCommands();

        foreach ($dependingCommands as $commandName) {
            $this->runDependingCommand($commandName);

            if ($this->hasError()) {
                return $this->getLastExitCode();
            }
        }
    }

    /**
     * @param string $command
     * @param array $arguments
     *
     * @throws \Exception
     *
     * @return void
     */
    protected function runDependingCommand($command, array $arguments = [])
    {
        $command = $this->getApplication()->find($command);
        $arguments['command'] = $command;
        $input = new ArrayInput($arguments);
        $command->run($input, $this->output);
    }

    /**
     * @return array
     */
    protected function getDependingCommands()
    {
        $noDiffOption = $this->input->getOption(self::OPTION_NO_DIFF);

        $dependingCommands = [
            ConvertConfigConsole::COMMAND_NAME,
            CreateDatabaseConsole::COMMAND_NAME,
            PostgresqlCompatibilityConsole::COMMAND_NAME,
            SchemaCopyConsole::COMMAND_NAME,
            BuildModelConsole::COMMAND_NAME,
        ];
        if ($noDiffOption === false) {
            $dependingCommands[] = DiffConsole::COMMAND_NAME;
        }
        $dependingCommands[] = MigrateConsole::COMMAND_NAME;

        return $dependingCommands;
    }

}
