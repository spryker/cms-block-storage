<?php

/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace Spryker\Zed\Application\Business\Model\ApplicationCheckStep;

use Symfony\Component\Process\Process;

class InstallDemoData extends AbstractApplicationCheckStep
{

    /**
     * @return bool
     */
    public function run()
    {
        $command = 'vendor/bin/console setup:install-demo-data';

        $this->info('Run ' . $command);

        $process = new Process($command);
        $process->setTimeout(600);

        $process->mustRun(function ($type, $buffer) {
            $this->info($buffer);
        });
    }

}
