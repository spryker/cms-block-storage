<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Navigation\Business\Model\Cache;

use Spryker\Zed\Navigation\Business\Model\Collector\NavigationCollectorInterface;

class NavigationCacheBuilder
{

    /**
     * @var \Spryker\Zed\Navigation\Business\Model\Collector\NavigationCollectorInterface
     */
    private $navigationCollector;

    /**
     * @var \Spryker\Zed\Navigation\Business\Model\Cache\NavigationCacheInterface
     */
    private $navigationCache;

    /**
     * @param \Spryker\Zed\Navigation\Business\Model\Collector\NavigationCollectorInterface $navigationCollector
     * @param \Spryker\Zed\Navigation\Business\Model\Cache\NavigationCacheInterface $navigationCache
     */
    public function __construct(NavigationCollectorInterface $navigationCollector, NavigationCacheInterface $navigationCache)
    {
        $this->navigationCollector = $navigationCollector;
        $this->navigationCache = $navigationCache;
    }

    /**
     * @return void
     */
    public function writeNavigationCache()
    {
        $navigation = $this->navigationCollector->getNavigation();
        $this->navigationCache->setNavigation($navigation);
    }

}
