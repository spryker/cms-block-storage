<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Unit\Spryker\Zed\Twig\Business\Model\TemplatePathMapBuilder;

use PHPUnit_Framework_TestCase;
use Spryker\Zed\Twig\Business\Model\TemplatePathMapBuilderInterface;
use Spryker\Zed\Twig\Business\Model\TemplatePathMapBuilder\TemplateNameBuilderInterface;
use Spryker\Zed\Twig\Business\Model\TemplatePathMapBuilder\TemplatePathMapBuilder;
use Symfony\Component\Finder\Finder;

/**
 * @group Unit
 * @group Spryker
 * @group Zed
 * @group Twig
 * @group Business
 * @group Model
 * @group TemplatePathMapBuilder
 * @group TemplatePathMapBuilderTest
 */
class TemplatePathMapBuilderTest extends PHPUnit_Framework_TestCase
{

    /**
     * @return void
     */
    public function testCanBeInstantiated()
    {
        $templateNameBuilder = $this->getTemplateNameBuilderMock();
        $directory = $this->getFixtureDirectory();
        $templateFinder = new TemplatePathMapBuilder(new Finder(), $templateNameBuilder, $directory);

        $this->assertInstanceOf(TemplatePathMapBuilderInterface::class, $templateFinder);
    }

    /**
     * @return void
     */
    public function testBuildReturnsArray()
    {
        $templateNameBuilder = $this->getTemplateNameBuilderMock();
        $templateNameBuilder->expects($this->once())->method('buildTemplateName')->willReturn('@Bundle/Controller/index.twig');

        $directory = $this->getFixtureDirectory();
        $templateFinder = new TemplatePathMapBuilder(new Finder(), $templateNameBuilder, $directory);

        $this->assertInternalType('array', $templateFinder->build());
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\Spryker\Zed\Twig\Business\Model\TemplatePathMapBuilder\TemplateNameBuilderInterface
     */
    protected function getTemplateNameBuilderMock()
    {
        $mockBuilder = $this->getMockBuilder(TemplateNameBuilderInterface::class)
            ->setMethods(['buildTemplateName']);

        return $mockBuilder->getMock();
    }

    /**
     * @return string
     */
    protected function getFixtureDirectory()
    {
        return __DIR__ . '/Fixtures';
    }

}
