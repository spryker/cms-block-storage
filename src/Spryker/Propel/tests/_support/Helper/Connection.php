<?php

/**
 * Copyright © 2017-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Propel\Helper;

use Codeception\Module;
use Codeception\TestInterface;
use Propel\Runtime\Propel;
use Silex\Application;
use Spryker\Zed\Propel\Communication\Plugin\ServiceProvider\PropelServiceProvider;

class Connection extends Module
{

    /**
     * @param \Codeception\TestInterface $test
     *
     * @return void
     */
    public function _before(TestInterface $test)
    {
        $propelServiceProvider = new PropelServiceProvider();
        $propelServiceProvider->boot(new Application());
    }

    /**
     * @param \Codeception\TestInterface $test
     *
     * @return void
     */
    public function _after(TestInterface $test)
    {
        Propel::closeConnections();
    }

}
