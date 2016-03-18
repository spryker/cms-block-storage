<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Application\Business\Model\ApplicationCheckStep;

use Symfony\Component\Process\Process;

class ExportStorage extends AbstractApplicationCheckStep
{

    /**
     * @return void
     */
    public function run()
    {
        $command = 'vendor/bin/console collector:storage:export';

        $this->info('Run ' . $command);

        $process = new Process($command);
        $process->setTimeout(600);

        $process->mustRun(function ($type, $buffer) {
            $this->info($buffer);
        });
    }

}
