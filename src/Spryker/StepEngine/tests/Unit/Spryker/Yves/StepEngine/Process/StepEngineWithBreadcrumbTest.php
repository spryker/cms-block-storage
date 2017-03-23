<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Unit\Spryker\Yves\StepEngine\Process;

use Spryker\Shared\Kernel\Transfer\AbstractTransfer;
use Spryker\Yves\StepEngine\Dependency\DataContainer\DataContainerInterface;
use Spryker\Yves\StepEngine\Process\StepBreadcrumbGeneratorInterface;
use Spryker\Yves\StepEngine\Process\StepEngine;
use Unit\Spryker\Yves\StepEngine\Process\Fixtures\StepMockWithBreadcrumb;

/**
 * @group Unit
 * @group Spryker
 * @group Yves
 * @group StepEngine
 * @group Process
 * @group StepEngineWithBreadcrumbTest
 */
class StepEngineWithBreadcrumbTest extends AbstractStepEngineTest
{

    const FORM_NAME = 'formName';

    /**
     * @return void
     */
    public function testProcessReturnViewDataWhenNoFormHandlerGiven()
    {
        $stepCollection = $this->getStepCollection();
        $stepCollection->addStep($this->getStepMock(true, true, true, self::STEP_ROUTE_A));

        $stepEngine = new StepEngine($stepCollection, $this->getDataContainerMock(), $this->getStepBreadcrumbGeneratorMock());
        $response = $stepEngine->process($this->getRequest(self::STEP_ROUTE_A));

        $this->assertInternalType('array', $response);
        $this->assertArrayHasKey(StepEngine::TEMPLATE_VARIABLE_STEP_BREADCRUMB, $response);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\Spryker\Yves\StepEngine\Process\StepBreadcrumbGeneratorInterface
     */
    protected function getStepBreadcrumbGeneratorMock()
    {
        return $this->getMockBuilder(StepBreadcrumbGeneratorInterface::class)->getMock();
    }

    /**
     * @param \Spryker\Shared\Kernel\Transfer\AbstractTransfer|null $dataTransfer
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|\Spryker\Yves\StepEngine\Dependency\DataContainer\DataContainerInterface
     */
    private function getDataContainerMock(AbstractTransfer $dataTransfer = null)
    {
        $dataContainerMock = $this->getMockBuilder(DataContainerInterface::class)->getMock();

        if ($dataTransfer) {
            $dataContainerMock->method('get')->willReturn($dataTransfer);
        } else {
            $dataContainerMock->method('get')->willReturn($this->getDataTransferMock());
        }

        return $dataContainerMock;
    }

    /**
     * @param bool $preCondition
     * @param bool $postCondition
     * @param bool $requireInput
     * @param string $stepRoute
     * @param string $escapeRoute
     *
     * @return \Spryker\Yves\StepEngine\Dependency\Step\StepWithBreadcrumbInterface
     */
    protected function getStepMockWithBreadcrumb($preCondition = true, $postCondition = true, $requireInput = true, $stepRoute = '', $escapeRoute = '')
    {
        return new StepMockWithBreadcrumb($preCondition, $postCondition, $requireInput, $stepRoute, $escapeRoute);
    }

}
