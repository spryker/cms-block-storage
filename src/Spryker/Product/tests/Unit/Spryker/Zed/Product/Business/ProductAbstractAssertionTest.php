<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Unit\Spryker\Zed\Product\Business;

use Codeception\TestCase\Test;
use Orm\Zed\Product\Persistence\SpyProductAbstractQuery;
use Propel\Runtime\ActiveQuery\Criteria;
use Spryker\Zed\Product\Business\Exception\MissingProductException;
use Spryker\Zed\Product\Business\Exception\ProductAbstractExistsException;
use Spryker\Zed\Product\Business\Product\Assertion\ProductAbstractAssertion;
use Spryker\Zed\Product\Persistence\ProductQueryContainerInterface;

/**
 * @group Unit
 * @group Spryker
 * @group Zed
 * @group Product
 * @group Business
 * @group ProductAbstractAssertionTest
 */
class ProductAbstractAssertionTest extends Test
{

    const SKU = 'sku';
    const ID_PRODUCT_ABSTRACT = 1;

    /**
     * @var \Spryker\Zed\Product\Persistence\ProductQueryContainerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $productQueryContainer;

    /**
     * @return void
     */
    protected function setUp()
    {
        parent::setUp();

        $this->productQueryContainer = $this->getMock(ProductQueryContainerInterface::class, [], [], '', false);
    }

    /**
     * @return void
     */
    public function testAssertSkuIsUnique()
    {
        $query = $this->getMock(SpyProductAbstractQuery::class, [], [], '', false);

        $query
            ->expects($this->once())
            ->method('count')
            ->willReturn(0);

        $this->productQueryContainer
            ->expects($this->once())
            ->method('queryProductAbstractBySku')
            ->with(self::SKU)
            ->willReturn($query);

        $productAbstractAssertion = new ProductAbstractAssertion($this->productQueryContainer);

        $productAbstractAssertion->assertSkuIsUnique(self::SKU);
    }

    /**
     * @return void
     */
    public function testAssertSkuIsUniqueShouldThrowException()
    {
        $this->expectException(ProductAbstractExistsException::class);
        $this->expectExceptionMessage(sprintf(
            'Product abstract with sku %s already exists',
            self::SKU
        ));

        $query = $this->getMock(SpyProductAbstractQuery::class, [], [], '', false);

        $query
            ->expects($this->once())
            ->method('count')
            ->willReturn(1);

        $this->productQueryContainer
            ->expects($this->once())
            ->method('queryProductAbstractBySku')
            ->with(self::SKU)
            ->willReturn($query);

        $productAbstractAssertion = new ProductAbstractAssertion($this->productQueryContainer);

        $productAbstractAssertion->assertSkuIsUnique(self::SKU);
    }

    /**
     * @return void
     */
    public function testAssertSkuIsUniqueWhenUpdatingProduct()
    {
        $query = $this->getMock(SpyProductAbstractQuery::class, [], [], '', false);

        $query
            ->expects($this->at(1))
            ->method('count')
            ->willReturn(0);

        $query
            ->expects($this->at(0))
            ->method('filterByIdProductAbstract')
            ->with(self::ID_PRODUCT_ABSTRACT, Criteria::NOT_EQUAL)
            ->willReturn($query);

        $this->productQueryContainer
            ->expects($this->once())
            ->method('queryProductAbstractBySku')
            ->with(self::SKU)
            ->willReturn($query);

        $productAbstractAssertion = new ProductAbstractAssertion($this->productQueryContainer);

        $productAbstractAssertion->assertSkuIsUniqueWhenUpdatingProduct(self::ID_PRODUCT_ABSTRACT, self::SKU);
    }

    /**
     * @return void
     */
    public function testAssertSkuIsUniqueWhenUpdatingProductShouldThrowException()
    {
        $this->expectException(ProductAbstractExistsException::class);
        $this->expectExceptionMessage(sprintf(
            'Product abstract with sku %s already exists',
            self::SKU
        ));

        $query = $this->getMock(SpyProductAbstractQuery::class, [], [], '', false);

        $query
            ->expects($this->at(1))
            ->method('count')
            ->willReturn(1);

        $query
            ->expects($this->at(0))
            ->method('filterByIdProductAbstract')
            ->with(self::ID_PRODUCT_ABSTRACT, Criteria::NOT_EQUAL)
            ->willReturn($query);

        $this->productQueryContainer
            ->expects($this->once())
            ->method('queryProductAbstractBySku')
            ->with(self::SKU)
            ->willReturn($query);

        $productAbstractAssertion = new ProductAbstractAssertion($this->productQueryContainer);

        $productAbstractAssertion->assertSkuIsUniqueWhenUpdatingProduct(self::ID_PRODUCT_ABSTRACT, self::SKU);
    }

    /**
     * @return void
     */
    public function testAssertProductExists()
    {
        $query = $this->getMock(SpyProductAbstractQuery::class, [], [], '', false);

        $query
            ->expects($this->at(1))
            ->method('count')
            ->willReturn(1);

        $query
            ->expects($this->at(0))
            ->method('filterByIdProductAbstract')
            ->with(self::ID_PRODUCT_ABSTRACT)
            ->willReturn($query);

        $this->productQueryContainer
            ->expects($this->once())
            ->method('queryProductAbstract')
            ->willReturn($query);

        $productAbstractAssertion = new ProductAbstractAssertion($this->productQueryContainer);

        $productAbstractAssertion->assertProductExists(self::ID_PRODUCT_ABSTRACT);
    }

    /**
     * @return void
     */
    public function testAssertProductExistsShouldThrowException()
    {
        $this->expectException(MissingProductException::class);
        $this->expectExceptionMessage(sprintf(
            'Product abstract with id "%s" does not exist.',
            self::ID_PRODUCT_ABSTRACT
        ));

        $query = $this->getMock(SpyProductAbstractQuery::class, [], [], '', false);

        $query
            ->expects($this->at(1))
            ->method('count')
            ->willReturn(0);

        $query
            ->expects($this->at(0))
            ->method('filterByIdProductAbstract')
            ->with(self::ID_PRODUCT_ABSTRACT)
            ->willReturn($query);

        $this->productQueryContainer
            ->expects($this->once())
            ->method('queryProductAbstract')
            ->willReturn($query);

        $productAbstractAssertion = new ProductAbstractAssertion($this->productQueryContainer);

        $productAbstractAssertion->assertProductExists(self::ID_PRODUCT_ABSTRACT);
    }

}
