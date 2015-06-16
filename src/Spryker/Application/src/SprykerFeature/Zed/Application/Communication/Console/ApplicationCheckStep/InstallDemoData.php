<?php

namespace SprykerFeature\Zed\Application\Communication\Console\ApplicationCheckStep;

use SprykerFeature\Zed\Application\Business\ApplicationFacade;

/**
 * @method ApplicationFacade getFacade()
 */
class InstallDemoData extends AbstractApplicationCheckStep
{
    public function run()
    {
        $this->getFacade()->runCheckStepInstallDemoData($this->logger);
    }
}
