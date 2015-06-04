<?php

namespace SprykerFeature\Zed\Stock\Business\Model;

use SprykerFeature\Zed\Product\Business\Exception\MissingProductException;
use SprykerFeature\Zed\Stock\Business\Exception\StockProductAlreadyExistsException;
use SprykerFeature\Zed\Stock\Business\Exception\StockProductNotFoundException;
use SprykerFeature\Zed\Stock\Persistence\Propel\SpyStockProduct;

interface ReaderInterface
{
    /**
     * @return array
     */
    public function getStockTypes();

    /**
     * @param string $sku
     *
     * @return bool
     */
    public function isNeverOutOfStock($sku);

    /**
     * @param string $sku
     *
     * @return array|\PropelObjectCollection
     */
    public function getStocksProduct($sku);

    /**
     * @param string $sku
     * @param string $stockType
     *
     * @return bool
     */
    public function hasStockProduct($sku, $stockType);

    /**
     * @param string $sku
     * @param string $stockType
     *
     * @return int
     */
    public function getIdStockProduct($sku, $stockType);

    /**
     * @param string $stockType
     *
     * @return int
     */
    public function getStockTypeIdByName($stockType);

    /**
     * @param string $sku
     *
     * @return int
     * @throws MissingProductException
     */
    public function getAbstractProductIdBySku($sku);

    /**
     * @param string $sku
     *
     * @return int
     * @throws MissingProductException
     */
    public function getConcreteProductIdBySku($sku);

    /**
     * @param int $idStockType
     * @param int $idProduct
     *
     * @throws StockProductAlreadyExistsException
     */
    public function checkStockDoesNotExist($idStockType, $idProduct);

    /**
     * @param int $idStockProduct
     *
     * @return SpyStockProduct
     * @throws StockProductNotFoundException
     */
    public function getStockProductById($idStockProduct);
}
