<?php
/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Unit\Spryker\Zed\Development\Business\IdeAutoCompletion\Generator;

use Codeception\TestCase\Test;
use Spryker\Zed\Development\Business\IdeAutoCompletion\Generator\BundleGenerator;
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
 * @group BundleGeneratorTest
 */
class BundleGeneratorTest extends Test
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
            ->with($this->equalTo('AutoCompletion.twig'));

        $generator = new BundleGenerator($twigEnvironmentMock, $this->getGeneratorOptions());
        $generator->generate([]);
    }

    /**
     * @return void
     */
    public function testNamespacePatternIsFilledWithApplicationName()
    {
        $twigEnvironmentMock = $this->createTwigEnvironmentMock();
        $twigEnvironmentMock
            ->expects($this->once())
            ->method('render')
            ->willReturnCallback(function ($templateName, $templateVariables) {
                $this->assertArrayHasKey('namespace', $templateVariables);
                $this->assertSame('Generated\FooApplication\Ide', $templateVariables['namespace']);
            });

        $generator = new BundleGenerator($twigEnvironmentMock, $this->getGeneratorOptions());
        $generator->generate([]);
    }

    /**
     * @return array
     */
    protected function getGeneratorOptions()
    {
        return [
            IdeAutoCompletionOptionConstants::APPLICATION_NAME => 'FooApplication',
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
