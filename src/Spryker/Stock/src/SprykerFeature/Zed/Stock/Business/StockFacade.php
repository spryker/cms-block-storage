<?php

namespace SprykerFeature\Zed\Stock\Business;

use Generated\Shared\Transfer\StockStockProductTransfer;
use Generated\Shared\Transfer\StockStockTypeTransfer;
use SprykerFeature\Zed\Availability\Dependency\Facade\AvailabilityToStockFacadeInterface;
use SprykerEngine\Zed\Kernel\Business\AbstractFacade;
use SprykerFeature\Zed\Stock\Persistence\Propel\SpyStock;
use SprykerFeature\Zed\Stock\Persistence\Propel\SpyStockProduct;
use SprykerFeature\Zed\StockSalesConnector\Dependency\Facade\StockToSalesFacadeInterface;

/**
 * @method StockDependencyContainer getDependencyContainer()
 */
class StockFacade extends AbstractFacade implements AvailabilityToStockFacadeInterface, StockToSalesFacadeInterface
{

    /**
     * @param string $sku
     *
     * @return bool
     */
    public function isNeverOutOfStock($sku)
    {
        return $this->getDependencyContainer()->getReaderModel()->isNeverOutOfStock($sku);
    }

    /**
     * @param string $sku
     *
     * @return int
     */
    public function calculateStockForProduct($sku)
    {
        return $this->getDependencyContainer()->getCalculatorModel()->calculateStockForProduct($sku);
    }

    /**
     * @param StockType $stockTypeTransfer
     *
     * @return int
     */
    public function createStockType(StockType $stockTypeTransfer)
    {
        return $this->getDependencyContainer()->getWriterModel()->createStockType($stockTypeTransfer);
    }

    /**
     * @param StockType $stockTypeTransfer
     *
     * @return int
     */
    public function updateStockType(StockType $stockTypeTransfer)
    {
        return $this->getDependencyContainer()->getWriterModel()->updateStockType($stockTypeTransfer);
    }

    /**
     * @param StockProduct $transferStockProduct
     *
     * @return int
     */
    public function createStockProduct(StockProduct $transferStockProduct)
    {
        return $this->getDependencyContainer()->getWriterModel()->createStockProduct($transferStockProduct);
    }

    /**
     * @param StockProduct $stockProductTransfer
     *
     * @return int
     */
    public function updateStockProduct(StockProduct $stockProductTransfer)
    {
        return $this->getDependencyContainer()->getWriterModel()->updateStockProduct($stockProductTransfer);
    }

    /**
     * @param string $sku
     * @param int $decrementBy
     * @param string $stockType
     */
    public function decrementStockProduct($sku, $stockType, $decrementBy = 1)
    {
        $this->getDependencyContainer()->getWriterModel()->decrementStock($sku, $stockType, $decrementBy);
    }

    /**
     * @param string $sku
     * @param int $incrementBy
     * @param string $stockType
     */
    public function incrementStockProduct($sku, $stockType, $incrementBy = 1)
    {
        $this->getDependencyContainer()->getWriterModel()->incrementStock($sku, $stockType, $incrementBy);
    }

    /**
     * @param string $sku
     * @param string $stockType
     *
     * @return bool
     */
    public function hasStockProduct($sku, $stockType)
    {
        return $this->getDependencyContainer()->getReaderModel()->hasStockProduct($sku, $stockType);
    }

    /**
     * @param string $sku
     * @param string $stockType
     *
     * @return int
     */
    public function getIdStockProduct($sku, $stockType)
    {
        return $this->getDependencyContainer()->getReaderModel()->getIdStockProduct($sku, $stockType);
    }
}
