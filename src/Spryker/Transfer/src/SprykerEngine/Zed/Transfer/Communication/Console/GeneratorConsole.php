<?php

/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace Spryker\Zed\Transfer\Communication\Console;

use Spryker\Zed\Transfer\Business\TransferFacade;
use Spryker\Zed\Console\Business\Model\Console;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @method TransferFacade getFacade()
 */
class GeneratorConsole extends Console
{

    const COMMAND_NAME = 'transfer:generate';

    /**
     * @return void
     */
    protected function configure()
    {
        parent::configure();
        $this
            ->setName(self::COMMAND_NAME)
            ->setHelp('<info>' . self::COMMAND_NAME . ' -h</info>');
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
        $facade = $this->getFacade();
        $messenger = $this->getMessenger();

        $facade->deleteGeneratedTransferObjects();
        $facade->generateTransferObjects($messenger);
    }

}
