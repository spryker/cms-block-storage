<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Unit\Spryker\Yves\Twig;

use PHPUnit_Framework_TestCase;
use Spryker\Yves\Twig\TwigConfig;

/**
 * @group Unit
 * @group Spryker
 * @group Yves
 * @group Twig
 * @group TwigConfigTest
 */
class TwigConfigTest extends PHPUnit_Framework_TestCase
{

    /**
     * @return void
     */
    public function testGetTemplatePathsShouldReturnAnArray()
    {
        $twigConfig = new TwigConfig();

        $this->assertInternalType('array', $twigConfig->getTemplatePaths());
    }

    /**
     * @return void
     */
    public function testGetCacheFilePathReturnsString()
    {
        $twigConfig = new TwigConfig();
        $this->assertInternalType('string', $twigConfig->getCacheFilePath());
    }

    /**
     * @return void
     */
    public function testIsPathCacheEnabledReturnsBoolean()
    {
        $twigConfig = new TwigConfig();
        $this->assertInternalType('bool', $twigConfig->isPathCacheEnabled());
    }

}
