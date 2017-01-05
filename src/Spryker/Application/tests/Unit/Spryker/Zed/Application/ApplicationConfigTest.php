<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Unit\Spryker\Zed\Application;

use PHPUnit_Framework_TestCase;
use Spryker\Zed\Application\ApplicationConfig;

/**
 * @group Unit
 * @group Spryker
 * @group Zed
 * @group Application
 * @group ApplicationConfigTest
 */
class ApplicationConfigTest extends PHPUnit_Framework_TestCase
{

    /**
     * @return \Spryker\Zed\Application\ApplicationConfig
     */
    private function getConfig()
    {
        return new ApplicationConfig();
    }

    /**
     * @return void
     */
    public function testGetMaxMenuLevelCountShouldReturnInteger()
    {
        $this->assertInternalType('integer', $this->getConfig()->getMaxMenuLevelCount());
    }

    /**
     * @return void
     */
    public function testGetNavigationSchemaPathPatternShouldReturnArrayWithOneEntry()
    {
        $navigationSchemaPathPatterns = $this->getConfig()->getNavigationSchemaPathPattern();
        $this->assertInternalType('array', $navigationSchemaPathPatterns);
        $this->assertCount(1, $navigationSchemaPathPatterns);
    }

    /**
     * @return void
     */
    public function testGetNavigationSchemaFileNamePatternShouldReturnString()
    {
        $this->assertInternalType('string', $this->getConfig()->getNavigationSchemaFileNamePattern());
    }

    /**
     * @return void
     */
    public function testGetRootNavigationSchemaShouldReturnString()
    {
        $this->assertInternalType('string', $this->getConfig()->getRootNavigationSchema());
    }

    /**
     * @return void
     */
    public function testGetCacheFileShouldReturnString()
    {
        $this->assertInternalType('string', $this->getConfig()->getCacheFile());
    }

    /**
     * @return void
     */
    public function testIsNavigationCacheEnabledShouldReturnBool()
    {
        $this->assertInternalType('bool', $this->getConfig()->isNavigationCacheEnabled());
    }

}
