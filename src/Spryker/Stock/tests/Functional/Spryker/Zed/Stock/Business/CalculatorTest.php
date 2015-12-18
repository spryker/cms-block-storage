<?php

/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace Functional\Spryker\Zed\Stock;

use Codeception\TestCase\Test;
use Orm\Zed\Product\Persistence\SpyProductAbstract;
use Orm\Zed\Product\Persistence\SpyProductAbstractQuery;
use Orm\Zed\Product\Persistence\SpyProduct;
use Orm\Zed\Product\Persistence\SpyProductQuery;
use Spryker\Zed\Stock\Business\StockFacade;
use Orm\Zed\Stock\Persistence\SpyStockProductQuery;
use Orm\Zed\Stock\Persistence\SpyStockQuery;
use Spryker\Zed\Stock\Persistence\StockQueryContainer;

/**
 * @group StockTest
 */
class CalculatorTest extends Test
{

    /**
     * @var StockFacade
     */
    private $stockFacade;

    /**
     * @var StockQueryContainer
     */
    private $stockQueryContainer;

    /**
     * @var SpyProduct
     */
    private $productEntity;

    /**
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->stockFacade = new StockFacade();
        $this->stockQueryContainer = new StockQueryContainer();
    }

    /**
     * @return void
     */
    public function testCalculateStock()
    {
        $this->setTestData();

        $stock = $this->stockFacade->calculateStockForProduct($this->productEntity->getSku());
        $this->assertEquals(30, $stock);
    }

    /**
     * @return void
     */
    protected function setTestData()
    {
        $abstractProduct = SpyProductAbstractQuery::create()
            ->filterBySku('test')
            ->findOne();

        if ($abstractProduct === null) {
            $abstractProduct = new SpyProductAbstract();
            $abstractProduct->setSku('test');
        }

        $abstractProduct->setAttributes('{}')
            ->save();

        $product = SpyProductQuery::create()
            ->filterBySku('test2')
            ->findOne();

        if ($product === null) {
            $product = new SpyProduct();
            $product->setSku('test2');
        }

        $product->setFkProductAbstract($abstractProduct->getIdProductAbstract())
            ->setAttributes('{}')
            ->save();

        $this->productEntity = $product;

        $stockType1 = SpyStockQuery::create()
            ->filterByName('warehouse1')
            ->findOneOrCreate();

        $stockType1->setName('warehouse1')->save();

        $stockType2 = SpyStockQuery::create()
            ->filterByName('warehouse2')
            ->findOneOrCreate();
        $stockType2->setName('warehouse2')->save();

        $stockProduct1 = SpyStockProductQuery::create()
            ->filterByFkStock($stockType1->getIdStock())
            ->filterByFkProduct($this->productEntity->getIdProduct())
            ->findOneOrCreate();
        $stockProduct1->setFkStock($stockType1->getIdStock())
            ->setQuantity(10)
            ->setFkProduct($this->productEntity->getIdProduct())
            ->save();

        $stockProduct2 = SpyStockProductQuery::create()
            ->filterByFkStock($stockType2->getIdStock())
            ->filterByFkProduct($this->productEntity->getIdProduct())
            ->findOneOrCreate();
        $stockProduct2->setFkStock($stockType2->getIdStock())
            ->setQuantity(20)
            ->setFkProduct($this->productEntity->getIdProduct())
            ->save();
    }

}
