<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Shared\ErrorHandler\ErrorRenderer;

use Exception;
use PHPUnit_Framework_TestCase;
use Spryker\Shared\ErrorHandler\ErrorRenderer\WebExceptionErrorRenderer;

/**
 * Auto-generated group annotations
 * @group SprykerTest
 * @group Shared
 * @group ErrorHandler
 * @group ErrorRenderer
 * @group WebErrorRendererTest
 * Add your own group annotations below this line
 */
class WebErrorRendererTest extends PHPUnit_Framework_TestCase
{

    /**
     * @return void
     */
    public function testRenderExceptionShouldReturnString()
    {
        $errorRenderer = new WebExceptionErrorRenderer();
        $exception = new Exception('ExceptionMessage');
        $exceptionString = $errorRenderer->render($exception);

        $this->assertInternalType('string', $exceptionString);
    }

}
