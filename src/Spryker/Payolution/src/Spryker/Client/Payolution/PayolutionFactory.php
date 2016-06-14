<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Client\Payolution;

use Spryker\Client\Kernel\AbstractFactory;
use Spryker\Client\Payolution\Session\PayolutionSession;
use Spryker\Client\Payolution\Zed\PayolutionStub;

class PayolutionFactory extends AbstractFactory
{

    /**
     * @return \Spryker\Client\Payolution\Session\PayolutionSession
     */
    public function createPayolutionSession()
    {
        return new PayolutionSession($this->getSessionClient());
    }

    /**
     * @return \Spryker\Client\Payolution\Zed\PayolutionStubInterface
     */
    public function createPayolutionStub()
    {
        return new PayolutionStub($this->createZedRequestClient());
    }

}
