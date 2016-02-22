<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Client\Shipment;

use Spryker\Client\Shipment\Zed\ShipmentStub;
use Spryker\Client\Kernel\AbstractFactory;

class ShipmentFactory extends AbstractFactory
{

    /**
     * @return \Spryker\Client\Shipment\Zed\ShipmentStubInterface
     */
    public function createZedStub()
    {
        $zedStub = $this->getProvidedDependency(ShipmentDependencyProvider::SERVICE_ZED);
        $cartStub = new ShipmentStub($zedStub);

        return $cartStub;
    }

}
