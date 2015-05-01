<?php

namespace SprykerFeature\Zed\Application\Business\Model\ApplicationCheckStep;

use Symfony\Component\Process\Process;

class ExportSearch extends AbstractApplicationCheckStep
{
    /**
     * @return void
     */
    public function run()
    {
        $command = 'vendor/bin/console frontend-exporter:export-search';

        $this->info('Run ' . $command);

        $process = new Process($command);
        $process->setTimeout(600);

        $process->mustRun(function ($type, $buffer) {
            $this->info($buffer);
        });
    }
}
