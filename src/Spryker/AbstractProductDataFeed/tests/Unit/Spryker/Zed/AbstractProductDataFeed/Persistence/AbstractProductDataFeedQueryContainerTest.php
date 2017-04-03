<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Unit\Spryker\Zed\AbstractProductDataFeed\Persistence;

use Codeception\TestCase\Test;
use Generated\Shared\Transfer\AbstractProductDataFeedTransfer;
use Orm\Zed\Product\Persistence\Base\SpyProductAbstractQuery;
use Spryker\Zed\AbstractProductDataFeed\Persistence\AbstractProductDataFeedQueryContainer;
use Spryker\Zed\Product\Persistence\ProductQueryContainer;

/**
 * @group Unit
 * @group Spryker
 * @group Zed
 * @group AbstractProductDataFeed
 * @group Persistence
 * @group AbstractProductDataFeedQueryContainerTest
 */
class AbstractProductDataFeedQueryContainerTest extends Test
{

    /**
     * @var \Spryker\Zed\AbstractProductDataFeed\Persistence\AbstractProductDataFeedQueryContainer
     */
    protected $productDataFeedQueryContainer;

    /**
     * @var \Generated\Shared\Transfer\AbstractProductDataFeedTransfer
     */
    protected $productDataFeedTransfer;

    /**
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->productDataFeedQueryContainer = $this->createProductDataFeedQueryContainer();
        $this->productDataFeedTransfer = $this->createProductDataFeedTransfer();
    }

    /**
     * @return void
     */
    public function testQueryAbstractProductDataFeed()
    {
        $query = $this->productDataFeedQueryContainer
            ->queryAbstractProductDataFeed($this->productDataFeedTransfer);

        $expectedJoinedTables = $this->getDefaultJoinedTables();
        $joinedTables = $this->getJoinedTablesNames($query);

        $this->assertInstanceOf(SpyProductAbstractQuery::class, $query);
        $this->assertEquals($expectedJoinedTables, $joinedTables);
    }

    /**
     * @return void
     */
    public function testQueryAbstractProductDataFeedWithJoinedCategories()
    {
        $this->productDataFeedTransfer->setIsJoinCategory(true);
        $query = $this->productDataFeedQueryContainer
            ->queryAbstractProductDataFeed($this->productDataFeedTransfer);

        $expectedJoinedTables = array_merge(
            $this->getDefaultJoinedTables(),
            $this->getCategoryJoinedTables()
        );
        $joinedTables = $this->getJoinedTablesNames($query);
        $expectedJoinedTables = $this->getSortedExpectedJoinedTables($expectedJoinedTables);

        $this->assertInstanceOf(SpyProductAbstractQuery::class, $query);
        $this->assertEquals($expectedJoinedTables, $joinedTables);
    }

    /**
     * @return void
     */
    public function testQueryAbstractProductDataFeedWithJoinedPrices()
    {
        $this->productDataFeedTransfer->setIsJoinPrice(true);
        $query = $this->productDataFeedQueryContainer
            ->queryAbstractProductDataFeed($this->productDataFeedTransfer);

        $expectedJoinedTables = array_merge(
            $this->getDefaultJoinedTables(),
            $this->getPriceJoinedTables()
        );
        $joinedTables = $this->getJoinedTablesNames($query);
        $expectedJoinedTables = $this->getSortedExpectedJoinedTables($expectedJoinedTables);

        $this->assertInstanceOf(SpyProductAbstractQuery::class, $query);
        $this->assertEquals($expectedJoinedTables, $joinedTables);
    }

    /**
     * @return void
     */
    public function testQueryAbstractProductDataFeedWithJoinedOptions()
    {
        $this->productDataFeedTransfer->setIsJoinOption(true);
        $query = $this->productDataFeedQueryContainer
            ->queryAbstractProductDataFeed($this->productDataFeedTransfer);

        $expectedJoinedTables = array_merge(
            $this->getDefaultJoinedTables(),
            $this->getOptionJoinedTables()
        );
        $joinedTables = $this->getJoinedTablesNames($query);
        $expectedJoinedTables = $this->getSortedExpectedJoinedTables($expectedJoinedTables);

        $this->assertInstanceOf(SpyProductAbstractQuery::class, $query);
        $this->assertEquals($expectedJoinedTables, $joinedTables);
    }

    /**
     * @return void
     */
    public function testQueryAbstractProductDataFeedWithJoinedImages()
    {
        $this->productDataFeedTransfer->setIsJoinImage(true);
        $query = $this->productDataFeedQueryContainer
            ->queryAbstractProductDataFeed($this->productDataFeedTransfer);

        $expectedJoinedTables = array_merge(
            $this->getDefaultJoinedTables(),
            $this->getImageJoinedTables()
        );
        $joinedTables = $this->getJoinedTablesNames($query);
        $expectedJoinedTables = $this->getSortedExpectedJoinedTables($expectedJoinedTables);

        $this->assertInstanceOf(SpyProductAbstractQuery::class, $query);
        $this->assertEquals($expectedJoinedTables, $joinedTables);
    }

    /**
     * @return void
     */
    public function testQueryAbstractProductDataFeedWithJoinedVariants()
    {
        $this->productDataFeedTransfer->setIsJoinProduct(true);
        $query = $this->productDataFeedQueryContainer
            ->queryAbstractProductDataFeed($this->productDataFeedTransfer);

        $expectedJoinedTables = array_merge(
            $this->getDefaultJoinedTables(),
            $this->getVariantJoinedTables(),
            $this->getImageJoinedTables()
        );
        $joinedTables = $this->getJoinedTablesNames($query);
        $expectedJoinedTables = $this->getSortedExpectedJoinedTables($expectedJoinedTables);

        $this->assertInstanceOf(SpyProductAbstractQuery::class, $query);
        $this->assertEquals($expectedJoinedTables, $joinedTables);
    }

    /**
     * @return void
     */
    public function testQueryAbstractProductDataFeedWithJoinedAll()
    {
        $this->productDataFeedTransfer->setIsJoinProduct(true);
        $this->productDataFeedTransfer->setIsJoinCategory(true);
        $this->productDataFeedTransfer->setIsJoinImage(true);
        $this->productDataFeedTransfer->setIsJoinPrice(true);
        $this->productDataFeedTransfer->setIsJoinOption(true);
        $query = $this->productDataFeedQueryContainer
            ->queryAbstractProductDataFeed($this->productDataFeedTransfer);

        $expectedJoinedTables = array_merge(
            $this->getDefaultJoinedTables(),
            $this->getCategoryJoinedTables(),
            $this->getImageJoinedTables(),
            $this->getPriceJoinedTables(),
            $this->getOptionJoinedTables(),
            $this->getVariantJoinedTables()
        );
        $joinedTables = $this->getJoinedTablesNames($query);
        $expectedJoinedTables = $this->getSortedExpectedJoinedTables($expectedJoinedTables);

        $this->assertInstanceOf(SpyProductAbstractQuery::class, $query);
        $this->assertEquals($expectedJoinedTables, $joinedTables);
    }

    /**
     * @return void
     */
    public function testQueryAbstractProductDataFeedWithLocaleId()
    {
        $this->productDataFeedTransfer->setLocaleId(46);
        $query = $this->productDataFeedQueryContainer
            ->queryAbstractProductDataFeed($this->productDataFeedTransfer);

        $this->assertInstanceOf(SpyProductAbstractQuery::class, $query);
        $this->assertEquals($this->getParamsForLocaleFilter(), $query->getParams());
    }

    /**
     * @return void
     */
    public function testQueryAbstractProductDataFeedWithDatesFilter()
    {
        $this->productDataFeedTransfer->setUpdatedFrom('2017-01-01');
        $this->productDataFeedTransfer->setUpdatedTo('2017-12-01');
        $query = $this->productDataFeedQueryContainer
            ->queryAbstractProductDataFeed($this->productDataFeedTransfer);

        $this->assertInstanceOf(SpyProductAbstractQuery::class, $query);
        $this->assertEquals($this->getParamsForDateFilter(), $query->getParams());
    }

    /**
     * @return \Spryker\Zed\AbstractProductDataFeed\Persistence\AbstractProductDataFeedQueryContainer
     */
    protected function createProductDataFeedQueryContainer()
    {
        $productQueryContainer = new ProductQueryContainer();
        $productDataFeedQueryContainer = new AbstractProductDataFeedQueryContainer($productQueryContainer);

        return $productDataFeedQueryContainer;
    }

    /**
     * @return \Generated\Shared\Transfer\AbstractProductDataFeedTransfer
     */
    protected function createProductDataFeedTransfer()
    {
        $productDataFeedTransfer = new AbstractProductDataFeedTransfer();

        return $productDataFeedTransfer;
    }

    /**
     * @param \Orm\Zed\Product\Persistence\Base\SpyProductAbstractQuery $query
     *
     * @return array
     */
    protected function getJoinedTablesNames(SpyProductAbstractQuery $query)
    {
        $tablesNames = [];
        $joins = $query->getJoins();

        foreach ($joins as $join) {
            $tablesNames[] = $join->getRightTableName();
        }
        asort($tablesNames);
        $tablesNames = array_values($tablesNames);

        return $tablesNames;
    }

    /**
     * @param array $tablesArray
     *
     * @return array
     */
    protected function getSortedExpectedJoinedTables($tablesArray)
    {
        asort($tablesArray);
        $tablesArray = array_values($tablesArray);

        return $tablesArray;
    }

    /**
     * @return array
     */
    protected function getDefaultJoinedTables()
    {
        return [
            'spy_product_abstract_localized_attributes',
        ];
    }

    /**
     * @return array
     */
    protected function getCategoryJoinedTables()
    {
        return [
            'spy_product_category',
            'spy_category',
            'spy_category_attribute',
        ];
    }

    /**
     * @return array
     */
    protected function getPriceJoinedTables()
    {
        return [
            'spy_price_product',
            'spy_price_type',
        ];
    }

    /**
     * @return array
     */
    protected function getOptionJoinedTables()
    {
        return [
            'spy_product_abstract_product_option_group',
            'spy_product_option_group',
            'spy_product_option_value',
        ];
    }

    /**
     * @return array
     */
    protected function getImageJoinedTables()
    {
        return [
            'spy_product_image',
            'spy_product_image_set',
            'spy_product_image_set_to_product_image',
        ];
    }

    /**
     * @return array
     */
    protected function getVariantJoinedTables()
    {
        return [
            'spy_product',
            'spy_product_localized_attributes',
        ];
    }

    /**
     * @return array
     */
    protected function getParamsForLocaleFilter()
    {
        return [
            [
                'table' => 'spy_product_abstract_localized_attributes',
                'column' => 'fk_locale',
                'value' => 46,
            ],
        ];
    }

    /**
     * @return array
     */
    protected function getParamsForDateFilter()
    {
        return [
            [
                'table' => 'spy_product_abstract_localized_attributes',
                'column' => 'updated_at',
                'value' => '2017-01-01',
            ],
            [
                'table' => 'spy_product_abstract_localized_attributes',
                'column' => 'updated_at',
                'value' => '2017-12-01',
            ],
        ];
    }

}
