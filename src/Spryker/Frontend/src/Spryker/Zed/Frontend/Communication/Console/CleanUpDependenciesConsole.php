<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Frontend\Communication\Console;

use Spryker\Zed\Kernel\Communication\Console\Console;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @method \Spryker\Zed\Storage\Business\StorageFacade getFacade()
 */
class CleanUpDependenciesConsole extends Console
{
    const COMMAND_NAME = 'frontend:cleanup-dependencies';
    const DESCRIPTION = 'This command will remove all frontend dependencies.';

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
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int|null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->info('Cleanup frontend dependencies');

        $frontendDependencyDirectories = [
            APPLICATION_ROOT_DIR . '/node_modules',
        ];
        $filesystem = new Filesystem();

        foreach ($frontendDependencyDirectories as $frontendDependencyDirectory) {
            if (is_dir($frontendDependencyDirectory)) {
                $filesystem->chmod($frontendDependencyDirectory, 0777);
                $filesystem->remove($frontendDependencyDirectory);
            }
        }

        return static::CODE_SUCCESS;
    }
}
