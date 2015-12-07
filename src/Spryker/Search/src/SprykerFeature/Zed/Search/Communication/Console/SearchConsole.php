<?php

/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace SprykerFeature\Zed\Search\Communication\Console;

use SprykerFeature\Zed\Search\Business\SearchFacade;
use SprykerFeature\Zed\Console\Business\Model\Console;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @method SearchFacade getFacade()
 */
class SearchConsole extends Console
{

    const COMMAND_NAME = 'setup:search';
    const DESCRIPTION = 'This command will run installer for search';
    const EXIT_CODE_ERROR = 1;
    const EXIT_CODE_SUCCESS = 0;

    /**
     * @return void
     */
    protected function configure()
    {
        $this->setName(self::COMMAND_NAME);
        $this->setDescription(self::DESCRIPTION);

        parent::configure();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $this->getFacade()->install($this->getMessenger());
        } catch (\Exception $e) {
            $this->error($e->getMessage());

            return self::EXIT_CODE_ERROR;
        }

        return self::EXIT_CODE_SUCCESS;
    }

}
