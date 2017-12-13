<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\CmsBlockStorage;

use Spryker\Zed\CmsBlockStorage\Dependency\Facade\CmsBlockStorageToEventBehaviorFacadeBridge;
use Spryker\Zed\CmsBlockStorage\Dependency\QueryContainer\CmsBlockStorageToCmsBlockQueryContainerBridge;
use Spryker\Zed\CmsBlockStorage\Dependency\Service\CmsBlockStorageToUtilSanitizeServiceBridge;
use Spryker\Zed\Kernel\AbstractBundleDependencyProvider;
use Spryker\Zed\Kernel\Container;

class CmsBlockStorageDependencyProvider extends AbstractBundleDependencyProvider
{
    const SERVICE_UTIL_SANITIZE = 'SERVICE_UTIL_SANITIZE';
    const FACADE_EVENT_BEHAVIOUR = 'FACADE_EVENT_BEHAVIOUR';

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    public function provideCommunicationLayerDependencies(Container $container)
    {
        $container = $this->addEventBehaviourFacade($container);
        $container = $this->addUtilSanitizeService($container);

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addUtilSanitizeService(Container $container): Container
    {
        $container[static::SERVICE_UTIL_SANITIZE] = function (Container $container) {
            return new CmsBlockStorageToUtilSanitizeServiceBridge($container->getLocator()->utilSanitize()->service());
        };

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addEventBehaviourFacade(Container $container): Container
    {
        $container[static::FACADE_EVENT_BEHAVIOUR] = function (Container $container) {
            return new CmsBlockStorageToEventBehaviorFacadeBridge($container->getLocator()->eventBehavior()->facade());
        };

        return $container;
    }
}
