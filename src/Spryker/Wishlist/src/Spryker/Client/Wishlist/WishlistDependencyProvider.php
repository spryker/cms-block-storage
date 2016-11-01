<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Client\Wishlist;

use Spryker\Client\Kernel\AbstractDependencyProvider;
use Spryker\Client\Kernel\Container;

class WishlistDependencyProvider extends AbstractDependencyProvider
{

    const SERVICE_ZED = 'SERVICE_ZED';
    const SESSION = 'SESSION';
    const STORAGE = 'STORAGE';
    const CLIENT_PRODUCT = 'CLIENT_PRODUCT';
    const CLIENT_CUSTOMER = 'CLIENT_CUSTOMER';

    /**
     * @param \Spryker\Client\Kernel\Container $container
     *
     * @return \Spryker\Client\Kernel\Container
     */
    public function provideServiceLayerDependencies(Container $container)
    {
        $container[self::SERVICE_ZED] = function (Container $container) {
            return $container->getLocator()->zedRequest()->client();
        };

        $container[self::SESSION] = function (Container $container) {
            return $container->getLocator()->session()->client();
        };

        $container[self::STORAGE] = function (Container $container) {
            return $container->getLocator()->storage()->client();
        };

        $container[self::CLIENT_PRODUCT] = function (Container $container) {
            return $container->getLocator()->product()->client();
        };

        $container[self::CLIENT_CUSTOMER] = function (Container $container) {
            return $container->getLocator()->customer()->client();
        };

        return $container;
    }

}
