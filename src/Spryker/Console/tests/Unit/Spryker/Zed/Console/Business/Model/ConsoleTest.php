<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Unit\Spryker\Zed\Console\Business\Model;

use Unit\Spryker\Zed\Console\Business\Model\Fixtures\ConsoleMock;

class ConsoleTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @return void
     */
    public function testGetCommunicationFactoryShouldReturnInstanceIfSet()
    {
        $console = $this->getConsole();
        $console->setFactory($this->getCommunicationFactoryMock());

        $this->assertInstanceOf(
            'Spryker\Zed\Kernel\Communication\AbstractCommunicationFactory',
            $console->getFactory()
        );
    }

    /**
     * @return void
     */
    public function testGetFacade()
    {
        $console = $this->getConsole();
        $console->setFacade($this->getFacadeMock());

        $this->assertInstanceOf('Spryker\Zed\Kernel\Business\AbstractFacade', $console->getFacade());
    }

    /**
     * @return void
     */
    public function testGetQueryContainerShouldReturnNullIfNotSet()
    {
        $console = $this->getConsole();

        $this->assertNull($console->getQueryContainer());
    }

    /**
     * @return void
     */
    public function testGetQueryContainerShouldReturnInstanceIfSet()
    {
        $console = $this->getConsole();
        $console->setQueryContainer($this->getQueryContainerMock());

        $this->assertInstanceOf(
            'Spryker\Zed\Kernel\Persistence\AbstractQueryContainer',
            $console->getQueryContainer()
        );
    }

    /**
     * @return \Spryker\Zed\Kernel\Communication\AbstractCommunicationFactory
     */
    private function getCommunicationFactoryMock()
    {
        return $this->getMock(
            'Spryker\Zed\Kernel\Communication\AbstractCommunicationFactory',
            [],
            [],
            '',
            false
        );
    }

    /**
     * @return \Spryker\Zed\Kernel\Business\AbstractFacade
     */
    private function getFacadeMock()
    {
        return $this->getMock('Spryker\Zed\Kernel\Business\AbstractFacade', [], [], '', false);
    }

    /**
     * @return \Spryker\Zed\Kernel\Persistence\AbstractQueryContainer
     */
    private function getQueryContainerMock()
    {
        return $this->getMock('Spryker\Zed\Kernel\Persistence\AbstractQueryContainer', [], [], '', false);
    }

    /**
     * @return \Unit\Spryker\Zed\Console\Business\Model\Fixtures\ConsoleMock
     */
    private function getConsole()
    {
        return new ConsoleMock('TestCommand');
    }

}
