<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Unit\Spryker\Zed\Development\Business\PhpMd\Rules\Design;

use PHPMD\AbstractNode;
use PHPUnit_Framework_TestCase;
use Spryker\Zed\Development\Business\PhpMd\Rules\Design\NumberOfChildren;

/**
 * @group Unit
 * @group Spryker
 * @group Zed
 * @group Development
 * @group Business
 * @group PhpMd
 * @group Rules
 * @group Design
 * @group NumberOfChildrenTest
 */
class NumberOfChildrenTest extends PHPUnit_Framework_TestCase
{

    const NUMBER_OF_CHILDREN = 2;
    const THRESHOLD = 1;

    /**
     * @dataProvider ignorableNodesProvider
     *
     * @param string $fullyQualifiedClassName
     *
     * @return void
     */
    public function testApplyDoesNotAddViolationIfNodeIsIgnorable($fullyQualifiedClassName)
    {
        $nodeMock = $this->getNodeMock($fullyQualifiedClassName);

        $numberOfChildrenMock = $this->getNumberOfChildrenMock();
        $numberOfChildrenMock->expects($this->never())->method('addViolation');
        $numberOfChildrenMock->apply($nodeMock);
    }

    /**
     * @return array
     */
    public function ignorableNodesProvider()
    {
        return [
            ['Zed\\Importer\\Business\\Importer\\AbstractImporter'],
            ['Zed\\Importer\\Business\\Installer\\AbstractInstaller'],
        ];
    }

    /**
     * @return void
     */
    public function testApplyAddsViolationWhenClassNotIgnorable()
    {
        $nodeMock = $this->getNodeMock('Foo');

        $numberOfChildrenMock = $this->getNumberOfChildrenMock();
        $numberOfChildrenMock->expects($this->once())->method('addViolation');
        $numberOfChildrenMock->apply($nodeMock);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\Spryker\Zed\Development\Business\PhpMd\Rules\Design\NumberOfChildren
     */
    protected function getNumberOfChildrenMock()
    {
        $mockBuilder = $this->getMockBuilder(NumberOfChildren::class);
        $mockBuilder->setMethods(['addViolation', 'getIntProperty']);

        $numberOfChildrenMock = $mockBuilder->getMock();
        $numberOfChildrenMock->expects($this->once())->method('getIntProperty')->willReturn(static::THRESHOLD);

        return $numberOfChildrenMock;
    }

    /**
     * @param string $fullyQualifiedClassName
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|\PHPMD\AbstractNode
     */
    protected function getNodeMock($fullyQualifiedClassName)
    {
        $mockBuilder = $this->getMockBuilder(AbstractNode::class);
        $mockBuilder->setMethods(['getMetric', 'getName', 'getNamespace', 'getNamespaceName', 'hasSuppressWarningsAnnotationFor', 'getFullQualifiedName', 'getParentName'])
            ->disableOriginalConstructor();

        $nodeMock = $mockBuilder->getMock();
        $nodeMock->expects($this->once())->method('getMetric')->willReturn(static::NUMBER_OF_CHILDREN);

        $nodeMock->method('getFullQualifiedName')->willReturn($fullyQualifiedClassName);

        return $nodeMock;
    }

}
