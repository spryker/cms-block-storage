<?php

/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace Functional\SprykerFeature\Zed\Stock;

use SprykerEngine\Zed\Kernel\Business\Factory as BusinessFactory;
use Codeception\TestCase\Test;
use SprykerEngine\Zed\Kernel\Locator;
use Orm\Zed\Product\Persistence\SpyAbstractProduct;
use Orm\Zed\Product\Persistence\SpyAbstractProductQuery;
use Orm\Zed\Product\Persistence\SpyProduct;
use Orm\Zed\Product\Persistence\SpyProductQuery;
use SprykerFeature\Zed\Stock\Business\StockFacade;
use Orm\Zed\Stock\Persistence\SpyStockProductQuery;
use Orm\Zed\Stock\Persistence\SpyStockQuery;
use SprykerFeature\Zed\Stock\Persistence\StockQueryContainer;
use SprykerEngine\Zed\Kernel\Persistence\Factory;

/**
 * @group StockTest
 */
class WriterTest extends Test
{

    /**
     * @var StockFacade
     */
    private $stockFacade;
    /**
     * @var StockQueryContainer
     */
    private $stockQueryContainer;

    public function setUp()
    {
        parent::setUp();

        $locator = Locator::getInstance();
        $this->stockFacade = new StockFacade(new BusinessFactory('Stock'), $locator);
        $this->stockQueryContainer = new StockQueryContainer(new Factory('Stock'), $locator);
    }

    public function testDecrementStock()
    {
        $this->setData();
        $stockProductEntity = SpyStockProductQuery::create()->findOne();
        $stockProductEntity->reload();
        $oldQuantity = $stockProductEntity->getQuantity();
        $product = SpyProductQuery::create()
            ->findOneByIdProduct($stockProductEntity->getFkProduct());
        $stockType = SpyStockQuery::create()
            ->findOneByIdStock($stockProductEntity->getFkStock());

        $this->stockFacade->decrementStockProduct($product->getSku(), $stockType->getName());

        $stockEntity = SpyStockProductQuery::create()->findOneByIdStockProduct($stockProductEntity->getIdStockProduct());
        $newQuantity = $stockEntity->getQuantity();

        $this->assertEquals($oldQuantity - 1, $newQuantity);
    }

    public function testIncrementStock()
    {
        $this->setData();
        $stockProductEntity = SpyStockProductQuery::create()->findOne();
        $stockProductEntity->reload();
        $oldQuantity = $stockProductEntity->getQuantity();
        $product = SpyProductQuery::create()->findOneByIdProduct($stockProductEntity->getFkProduct());
        $stockType = SpyStockQuery::create()->findOneByIdStock($stockProductEntity->getFkStock());

        $this->stockFacade->incrementStockProduct($product->getSku(), $stockType->getName());

        $stockEntity = SpyStockProductQuery::create()->findOneByIdStockProduct($stockProductEntity->getIdStockProduct());
        $newQuantity = $stockEntity->getQuantity();

        $this->assertEquals($oldQuantity + 1, $newQuantity);
    }

    protected function setData()
    {
        $abstractProduct = SpyAbstractProductQuery::create()
            ->filterBySku('test')
            ->findOne();

        if ($abstractProduct === null) {
            $abstractProduct = new SpyAbstractProduct();
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

        $product->setFkAbstractProduct($abstractProduct->getIdAbstractProduct())
            ->setAttributes('{}')
            ->save();

        $product = SpyProductQuery::create()
            ->filterBySku('test2')
            ->findOne();

        if ($product === null) {
            $product = new SpyProduct();
            $product->setSku('test2');
        }

        $product->setFkAbstractProduct($abstractProduct->getIdAbstractProduct())
            ->setAttributes('{}')
            ->save();

        $stockType1 = SpyStockQuery::create()
            ->filterByName('warehouse1')
            ->findOneOrCreate();
        $stockType1->setName('warehouse1')
            ->save();

        $stockType2 = SpyStockQuery::create()
            ->filterByName('warehouse2')
            ->findOneOrCreate();
        $stockType2->setName('warehouse2')->save();

        $stockProduct1 = SpyStockProductQuery::create()
            ->filterByFkStock($stockType1->getIdStock())
            ->filterByFkProduct($product->getIdProduct())
            ->findOneOrCreate();

        $stockProduct1->setFkStock($stockType1->getIdStock())
            ->setQuantity(10)
            ->setFkProduct($product->getIdProduct())
            ->save();

        $stockProduct2 = SpyStockProductQuery::create()
            ->filterByFkStock($stockType2->getIdStock())
            ->filterByFkProduct($product->getIdProduct())
            ->findOneOrCreate();

        $stockProduct2->setFkStock($stockType2->getIdStock())
            ->setQuantity(20)
            ->setFkProduct($product->getIdProduct())
            ->save();
    }

}
