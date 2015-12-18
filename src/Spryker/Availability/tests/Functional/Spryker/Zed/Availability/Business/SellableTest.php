<?php

/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace Functional\Spryker\Zed\Availability;

use Spryker\Zed\Kernel\AbstractFunctionalTest;
use Spryker\Zed\Kernel\Container;
use Spryker\Zed\Availability\AvailabilityDependencyProvider;
use Spryker\Zed\Availability\Business\AvailabilityBusinessFactory;
use Spryker\Zed\Availability\Business\AvailabilityFacade;
use Orm\Zed\Product\Persistence\SpyProductAbstract;
use Orm\Zed\Product\Persistence\SpyProductAbstractQuery;
use Orm\Zed\Product\Persistence\SpyProduct;
use Orm\Zed\Product\Persistence\SpyProductQuery;
use Orm\Zed\Stock\Persistence\SpyStock;
use Orm\Zed\Stock\Persistence\SpyStockProduct;
use Orm\Zed\Stock\Persistence\SpyStockProductQuery;
use Orm\Zed\Stock\Persistence\SpyStockQuery;

/**
 * @group Spryker
 * @group Zed
 * @group Business
 * @group Availability
 * @group SellableTest
 */
class SellableTest extends AbstractFunctionalTest
{

    /**
     * @var AvailabilityFacade
     */
    private $availabilityFacade;

    /**
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->availabilityFacade = new AvailabilityFacade();

        $container = new Container();
        $businessFactory = new AvailabilityBusinessFactory();
        $dependencyProvider = new AvailabilityDependencyProvider();
        $dependencyProvider->provideBusinessLayerDependencies($container);
        $businessFactory->setContainer($container);

        $this->availabilityFacade->setBusinessFactory($businessFactory);
    }

    /**
     * @return void
     */
    public function testIsProductSellable()
    {
        $this->setTestData();
        $stockProductEntity = SpyStockProductQuery::create()->findOneOrCreate();
        $stockProductEntity->setIsNeverOutOfStock(true)->save();

        $productEntity = SpyProductQuery::create()->findOneByIdProduct($stockProductEntity->getFkProduct());
        $isSellable = $this->availabilityFacade->isProductSellable($productEntity->getSku(), 100);

        $this->assertTrue($isSellable);
    }

    /**
     * @return void
     */
    public function testCalculateRealStock()
    {
        $this->setTestData();
        $stockProductEntity = SpyStockProductQuery::create()->findOneOrCreate();
        $stockProductEntity->setIsNeverOutOfStock(false)->setQuantity(10)->save();
        $productEntity = SpyProductQuery::create()->findOneByIdProduct($stockProductEntity->getFkProduct());
        $isSellable = $this->availabilityFacade->isProductSellable($productEntity->getSku(), 1);

        $this->assertTrue($isSellable);
    }

    /**
     * @return void
     */
    public function testProductIsNotSellableIfStockNotSufficient()
    {
        $this->setTestData();

        $abstractProduct = new SpyProductAbstract();
        $abstractProduct
            ->setSku('AP1337')
            ->setAttributes('{}');

        $concreteProduct = new SpyProduct();
        $concreteProduct
            ->setSku('P1337')
            ->setSpyProductAbstract($abstractProduct)
            ->setAttributes('{}');

        $stock = new SpyStock();
        $stock
            ->setName('TestStock1');

        $stockProduct = new SpyStockProduct();
        $stockProduct
            ->setStock($stock)
            ->setSpyProduct($concreteProduct)
            ->setQuantity(5)
            ->save();

        $this->assertFalse($this->availabilityFacade->isProductSellable('P1337', 6));
    }

    /**
     * @return void
     */
    protected function setTestData()
    {
        $abstractProduct = SpyProductAbstractQuery::create()
            ->filterBySku('test2')
            ->findOne();

        if (!$abstractProduct) {
            $abstractProduct = new SpyProductAbstract();
        }

        $abstractProduct
            ->setSku('test2')
            ->setAttributes('{}')
            ->save();

        $productEntity = SpyProductQuery::create()
            ->filterByFkProductAbstract($abstractProduct->getIdProductAbstract())
            ->filterBySku('test1')
            ->findOne();

        if (!$productEntity) {
            $productEntity = new SpyProduct();
        }

        $productEntity
            ->setFkProductAbstract($abstractProduct->getIdProductAbstract())
            ->setSku('test1')
            ->setAttributes('{}')
            ->save();

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
            ->filterByFkProduct($productEntity->getIdProduct())
            ->findOneOrCreate();
        $stockProduct1->setFkStock($stockType1->getIdStock())
            ->setQuantity(10)
            ->setFkProduct($productEntity->getIdProduct())
            ->save();
        $stockProduct2 = SpyStockProductQuery::create()
            ->filterByFkStock($stockType2->getIdStock())
            ->filterByFkProduct($productEntity->getIdProduct())
            ->findOneOrCreate();
        $stockProduct2->setFkStock($stockType2->getIdStock())
            ->setQuantity(20)
            ->setFkProduct($productEntity->getIdProduct())
            ->save();
    }

}
