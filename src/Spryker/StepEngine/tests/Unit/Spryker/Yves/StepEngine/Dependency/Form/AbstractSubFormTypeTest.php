<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Unit\Spryker\Yves\StepEngine\Dependency\Form;

use PHPUnit_Framework_TestCase;
use Spryker\Yves\StepEngine\Dependency\Form\AbstractSubFormType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

/**
 * @group Unit
 * @group Spryker
 * @group Yves
 * @group StepEngine
 * @group Dependency
 * @group Form
 * @group AbstractSubFormTypeTest
 */
class AbstractSubFormTypeTest extends PHPUnit_Framework_TestCase
{

    const TEMPLATE_PATH = 'abstract/sub/form/type';

    /**
     * @return void
     */
    public function testBuildViewVarsContainExpectedPathToTemplate()
    {
        $abstractSubFormTypeMock = $this->getAbstractSubFormTypeMock();

        $view = new FormView();
        $abstractSubFormTypeMock->buildView($view, $this->getFormMock(), []);

        $this->assertSame($view->vars[AbstractSubFormType::TEMPLATE_PATH], self::TEMPLATE_PATH);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\Spryker\Yves\StepEngine\Dependency\Form\AbstractSubFormType
     */
    private function getAbstractSubFormTypeMock()
    {
        $abstractSubFormTypeMock = $this->getMockForAbstractClass(AbstractSubFormType::class, [], '', true, true, true, ['getTemplatePath']);
        $abstractSubFormTypeMock->method('getTemplatePath')->willReturn(self::TEMPLATE_PATH);

        return $abstractSubFormTypeMock;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\Symfony\Component\Form\FormInterface
     */
    private function getFormMock()
    {
        return $this->getMockBuilder(FormInterface::class)->getMock();
    }

}
