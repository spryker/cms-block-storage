<?php

/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace SprykerFeature\Zed\Stock\Business;

use SprykerFeature\Zed\Stock\Business\Model\Writer;
use SprykerFeature\Zed\Stock\Business\Model\Reader;
use SprykerFeature\Zed\Stock\Business\Model\Calculator;
use SprykerEngine\Zed\Kernel\Business\AbstractBusinessDependencyContainer;
use SprykerFeature\Zed\Stock\Dependency\Facade\StockToProductInterface;
use SprykerFeature\Zed\Stock\Persistence\StockQueryContainer;
use SprykerFeature\Zed\Stock\Business\Model\ReaderInterface;
use SprykerFeature\Zed\Stock\Business\Model\WriterInterface;
use SprykerFeature\Zed\Stock\Business\Model\CalculatorInterface;
use SprykerFeature\Zed\Stock\Dependency\Facade\StockToTouchInterface;
use SprykerFeature\Zed\Stock\StockConfig;

/**
 * @method StockConfig getConfig()
 */
class StockDependencyContainer extends AbstractBusinessDependencyContainer
{

    /**
     * @return CalculatorInterface
     */
    public function getCalculatorModel()
    {
        return new Calculator(
            $this->getReaderModel()
        );
    }

    /**
     * @return ReaderInterface
     */
    public function getReaderModel()
    {
        return new Reader(
            $this->getQueryContainer(),
            $this->getProductFacade()
        );
    }

    /**
     * @return WriterInterface
     */
    public function getWriterModel()
    {
        return new Writer(
            $this->getQueryContainer(),
            $this->getReaderModel(),
            $this->getTouchFacade(),
            $this->getLocator()
        );
    }

    /**
     * @return StockQueryContainer
     */
    protected function getQueryContainer()
    {
        return $this->getLocator()->stock()->queryContainer();
    }

    /**
     * @return StockToProductInterface
     */
    protected function getProductFacade()
    {
        return $this->getLocator()->product()->facade();
    }

    /**
     * @return StockToTouchInterface
     */
    protected function getTouchFacade()
    {
        return $this->getLocator()->touch()->facade();
    }

}
