<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\CmsBlockStorage;

use Spryker\Zed\CmsBlockStorage\Dependency\Facade\CmsBlockStorageToEventBehaviorFacadeBridge;
use Spryker\Zed\CmsBlockStorage\Dependency\Facade\CmsBlockStorageToStoreFacadeBridge;
use Spryker\Zed\CmsBlockStorage\Dependency\Service\CmsBlockStorageToUtilSanitizeServiceBridge;
use Spryker\Zed\Kernel\AbstractBundleDependencyProvider;
use Spryker\Zed\Kernel\Container;

/**
 * @method \Spryker\Zed\CmsBlockStorage\CmsBlockStorageConfig getConfig()
 */
class CmsBlockStorageDependencyProvider extends AbstractBundleDependencyProvider
{
    /**
     * @var string
     */
    public const SERVICE_UTIL_SANITIZE = 'SERVICE_UTIL_SANITIZE';

    /**
     * @var string
     */
    public const FACADE_EVENT_BEHAVIOR = 'FACADE_EVENT_BEHAVIOR';

    /**
     * @var string
     */
    public const PLUGIN_CONTENT_WIDGET_DATA_EXPANDER = 'PLUGIN_CONTENT_WIDGET_DATA_EXPANDER';

    /**
     * @var string
     */
    public const FACADE_STORE = 'FACADE_STORE';

    public function provideCommunicationLayerDependencies(Container $container): Container
    {
        $container = $this->addEventBehaviorFacade($container);

        return $container;
    }

    public function provideBusinessLayerDependencies(Container $container): Container
    {
        $container = $this->addUtilSanitizeService($container);
        $container = $this->addContentWidgetDataExpanderPlugin($container);
        $container = $this->addStoreFacade($container);

        return $container;
    }

    protected function addUtilSanitizeService(Container $container): Container
    {
        $container->set(static::SERVICE_UTIL_SANITIZE, function (Container $container) {
            return new CmsBlockStorageToUtilSanitizeServiceBridge($container->getLocator()->utilSanitize()->service());
        });

        return $container;
    }

    protected function addEventBehaviorFacade(Container $container): Container
    {
        $container->set(static::FACADE_EVENT_BEHAVIOR, function (Container $container) {
            return new CmsBlockStorageToEventBehaviorFacadeBridge($container->getLocator()->eventBehavior()->facade());
        });

        return $container;
    }

    protected function addContentWidgetDataExpanderPlugin(Container $container): Container
    {
        $container->set(static::PLUGIN_CONTENT_WIDGET_DATA_EXPANDER, function () {
            return $this->getContentWidgetDataExpanderPlugins();
        });

        return $container;
    }

    protected function addStoreFacade(Container $container): Container
    {
        $container->set(static::FACADE_STORE, function (Container $container) {
            return new CmsBlockStorageToStoreFacadeBridge($container->getLocator()->store()->facade());
        });

        return $container;
    }

    /**
     * @return array<\Spryker\Zed\CmsBlockStorage\Dependency\Plugin\CmsBlockStorageDataExpanderPluginInterface>
     */
    protected function getContentWidgetDataExpanderPlugins(): array
    {
        return [];
    }
}
