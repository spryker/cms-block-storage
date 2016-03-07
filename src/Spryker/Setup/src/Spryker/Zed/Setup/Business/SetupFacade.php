<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Setup\Business;

use Spryker\Zed\Kernel\Business\AbstractFacade;
use Symfony\Component\HttpFoundation\Request;

/**
 * @method \Spryker\Zed\Setup\Business\SetupBusinessFactory getFactory()
 */
class SetupFacade extends AbstractFacade implements SetupFacadeInterface
{

    /**
     * @api
     *
     * @param array $roles
     *
     * @return mixed
     */
    public function generateCronjobs(array $roles)
    {
        return $this->getFactory()->createModelCronjobs()->generateCronjobs($roles);
    }

    /**
     * @api
     *
     * @return string
     */
    public function enableJenkins()
    {
        return $this->getFactory()->createModelCronjobs()->enableJenkins();
    }

    /**
     * @api
     *
     * @return string
     */
    public function disableJenkins()
    {
        return $this->getFactory()->createModelCronjobs()->disableJenkins();
    }

    /**
     * @api
     *
     * @return void
     */
    public function removeGeneratedDirectory()
    {
        $this->getFactory()->createModelGeneratedDirectoryRemover()->execute();
    }

    /**
     * @api
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return string
     */
    public function getRepeatData(Request $request)
    {
        return $this->getFactory()->getTransferObjectRepeater()
            ->getRepeatData($request->query->get('mvc', null)); // TODO FW Validation
    }

    /**
     * @api
     *
     * @return \Symfony\Component\Console\Command\Command[]
     */
    public function getConsoleCommands()
    {
        return $this->getFactory()->getConsoleCommands();
    }

}
