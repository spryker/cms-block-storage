<?php
/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Unit\Spryker\Zed\Development\Business\IdeAutoCompletion\Generator;

use Codeception\TestCase\Test;
use Spryker\Zed\Development\Business\IdeAutoCompletion\Generator\BundleMethodGenerator;
use Spryker\Zed\Development\Business\IdeAutoCompletion\IdeAutoCompletionConstants;
use Spryker\Zed\Development\Business\IdeAutoCompletion\IdeAutoCompletionOptionConstants;
use Twig_Environment;

/**
 * @group Unit
 * @group Spryker
 * @group Zed
 * @group Development
 * @group Business
 * @group IdeAutoCompletion
 * @group Generator
 * @group BundleMethodGeneratorTest
 */
class BundleMethodGeneratorTest extends Test
{

    /**
     * @return void
     */
    public function testTemplateNameIsDerivedFromGeneratorName()
    {
        $twigEnvironmentMock = $this->createTwigEnvironmentMock();
        $twigEnvironmentMock
            ->expects($this->once())
            ->method('render')
            ->with($this->equalTo('BundleAutoCompletion.twig'));

        $generator = new BundleMethodGenerator($twigEnvironmentMock, $this->getGeneratorOptions());
        $generator->generate([]);
    }

    /**
     * @return array
     */
    protected function getGeneratorOptions()
    {
        return [
            IdeAutoCompletionOptionConstants::APPLICATION_NAME => 'BarApplication',
            IdeAutoCompletionOptionConstants::TARGET_NAMESPACE_PATTERN => sprintf(
                'Generated\%s\Ide',
                IdeAutoCompletionConstants::APPLICATION_NAME_PLACEHOLDER
            ),
        ];
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\Twig_Environment
     */
    protected function createTwigEnvironmentMock()
    {
        return $this
            ->getMockBuilder(Twig_Environment::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

}
