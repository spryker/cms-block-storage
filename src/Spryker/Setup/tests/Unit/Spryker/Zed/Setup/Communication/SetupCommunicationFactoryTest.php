<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Unit\Spryker\Zed\Setup\Communication;

use PHPUnit_Framework_TestCase;
use Spryker\Zed\Setup\Communication\SetupCommunicationFactory;

/**
 * @group Unit
 * @group Spryker
 * @group Zed
 * @group Setup
 * @group Communication
 * @group SetupCommunicationFactoryTest
 */
class SetupCommunicationFactoryTest extends PHPUnit_Framework_TestCase
{

    /**
     * @return void
     */
    public function testGetSetupInstallCommandNamesMustReturnArray()
    {
        $communicationFactory = new SetupCommunicationFactory();

        $this->assertInternalType('array', $communicationFactory->getSetupInstallCommandNames());
    }

}
