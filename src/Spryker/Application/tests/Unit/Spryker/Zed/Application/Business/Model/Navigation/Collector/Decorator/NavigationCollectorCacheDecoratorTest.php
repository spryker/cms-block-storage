<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Unit\Spryker\Zed\Application\Business\Model\Navigation\Collector\Decorator;

use Spryker\Zed\Application\Business\Model\Navigation\Collector\Decorator\NavigationCollectorCacheDecorator;

/**
 * @group Unit
 * @group Spryker
 * @group Zed
 * @group Application
 * @group Business
 * @group Model
 * @group Navigation
 * @group Collector
 * @group Decorator
 * @group NavigationCollectorCacheDecoratorTest
 */
class NavigationCollectorCacheDecoratorTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @return void
     */
    public function testIfCacheIsNotEnabledGetNavigationMustReturnNavigationFromCollector()
    {
        $navigationCacheMock = $this->getMock(
            'Spryker\Zed\Application\Business\Model\Navigation\Cache\NavigationCacheInterface',
            ['isEnabled', 'setNavigation', 'getNavigation']
        );
        $navigationCacheMock->expects($this->once())
            ->method('isEnabled')
            ->will($this->returnValue(false));
        $navigationCacheMock->expects($this->never())
            ->method('getNavigation');

        $navigationCollectorMock = $this->getMock(
            'Spryker\Zed\Application\Business\Model\Navigation\Collector\NavigationCollectorInterface',
            ['getNavigation']
        );
        $navigationCollectorMock->expects($this->once())
            ->method('getNavigation')
            ->will($this->returnValue([]));

        $navigationCollectorCacheDecorator = new NavigationCollectorCacheDecorator($navigationCollectorMock, $navigationCacheMock);

        $this->assertInternalType(
            'array',
            $navigationCollectorCacheDecorator->getNavigation()
        );
    }

    /**
     * @return void
     */
    public function testIfCacheIsEnabledGetNavigationMustReturnNavigationFromCache()
    {
        $navigationCacheMock = $this->getMock(
            'Spryker\Zed\Application\Business\Model\Navigation\Cache\NavigationCacheInterface',
            ['isEnabled', 'setNavigation', 'getNavigation']
        );
        $navigationCacheMock->expects($this->once())
            ->method('isEnabled')
            ->will($this->returnValue(true));
        $navigationCacheMock->expects($this->once())
            ->method('getNavigation')
            ->will($this->returnValue([]));

        $navigationCollectorMock = $this->getMock(
            'Spryker\Zed\Application\Business\Model\Navigation\Collector\NavigationCollectorInterface',
            ['getNavigation']
        );
        $navigationCollectorMock->expects($this->never())
            ->method('getNavigation');

        $navigationCollectorCacheDecorator = new NavigationCollectorCacheDecorator($navigationCollectorMock, $navigationCacheMock);

        $this->assertInternalType(
            'array',
            $navigationCollectorCacheDecorator->getNavigation()
        );
    }

}
