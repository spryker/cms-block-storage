<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Braintree;

use Spryker\Zed\Braintree\Dependency\Facade\BraintreeToSalesAggregatorBridge;
use Spryker\Zed\Kernel\AbstractBundleDependencyProvider;
use Spryker\Zed\Kernel\Container;

class BraintreeDependencyProvider extends AbstractBundleDependencyProvider
{

    const FACADE_SALES_AGGREGATOR = 'sales aggregor facade';

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    public function provideCommunicationLayerDependencies(Container $container)
    {
        $container[static::FACADE_SALES_AGGREGATOR] = function (Container $container) {
            return new BraintreeToSalesAggregatorBridge($container->getLocator()->salesAggregator()->facade());
        };

        return $container;
    }

}
