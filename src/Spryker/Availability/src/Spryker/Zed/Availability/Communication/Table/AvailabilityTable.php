<?php
/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Availability\Communication\Table;

use Orm\Zed\Availability\Persistence\SpyAvailability;
use Orm\Zed\Product\Persistence\Map\SpyProductLocalizedAttributesTableMap;
use Orm\Zed\Product\Persistence\Map\SpyProductTableMap;
use Orm\Zed\Product\Persistence\SpyProductAbstractQuery;
use Spryker\Shared\Url\Url;
use Spryker\Zed\Availability\Persistence\AvailabilityQueryContainer;
use Spryker\Zed\Gui\Communication\Table\AbstractTable;
use Spryker\Zed\Gui\Communication\Table\TableConfiguration;

class AvailabilityTable extends AbstractTable
{

    const TABLE_COL_ACTION = 'Actions';
    const URL_PARAM_ID_PRODUCT = 'id-product';
    const URL_PARAM_ID_PRODUCT_ABSTRACT = 'id-abstract';
    const URL_PARAM_SKU = 'sku';

    /**
     * @var int
     */
    protected $idProductAbstract;

    /**
     * @var \Orm\Zed\Product\Persistence\SpyProductAbstractQuery
     */
    protected $queryProductAbstractAvailability;

    /**
     * @param \Orm\Zed\Product\Persistence\SpyProductAbstractQuery $queryProductAbstractAvailability
     * @param int $idProductAbstract
     */
    public function __construct(SpyProductAbstractQuery $queryProductAbstractAvailability, $idProductAbstract)
    {
        $this->queryProductAbstractAvailability = $queryProductAbstractAvailability;
        $this->idProductAbstract = $idProductAbstract;
    }

    /**
     * @param \Spryker\Zed\Gui\Communication\Table\TableConfiguration $config
     *
     * @return \Spryker\Zed\Gui\Communication\Table\TableConfiguration
     */
    protected function configure(TableConfiguration $config)
    {
        $url = Url::generate('/availability-table', [
            AvailabilityAbstractTable::URL_PARAM_ID_PRODUCT_ABSTRACT => $this->idProductAbstract
        ])->build();

        $config->setUrl($url);
        $config->setHeader([
            AvailabilityQueryContainer::CONCRETE_SKU => 'SKU',
            AvailabilityQueryContainer::CONCRETE_NAME => 'Name',
            AvailabilityQueryContainer::CONCRETE_AVAILABILITY => 'Availability',
            AvailabilityQueryContainer::STOCK_QUANTITY => 'Current Stock',
            AvailabilityQueryContainer::RESERVATION_QUANTITY => 'Reserved Products',
            self::TABLE_COL_ACTION => 'Actions'
        ]);

        $config->setSortable([
            AvailabilityQueryContainer::CONCRETE_SKU,
            AvailabilityQueryContainer::CONCRETE_NAME,
            AvailabilityQueryContainer::STOCK_QUANTITY,
            AvailabilityQueryContainer::RESERVATION_QUANTITY
        ]);

        $config->setSearchable([
            SpyProductTableMap::COL_SKU,
            SpyProductLocalizedAttributesTableMap::COL_NAME
        ]);

        $config->setDefaultSortColumnIndex(0);
        $config->addRawColumn(self::TABLE_COL_ACTION);
        $config->setDefaultSortDirection(TableConfiguration::SORT_DESC);

        return $config;
    }

    /**
     * @param \Spryker\Zed\Gui\Communication\Table\TableConfiguration $config
     *
     * @return array
     */
    protected function prepareData(TableConfiguration $config)
    {
        $result = [];

        $queryResult = $this->runQuery($this->queryProductAbstractAvailability, $config, true);

        foreach ($queryResult as $productItem) {
            $result[] = [
                AvailabilityQueryContainer::CONCRETE_SKU => $productItem[AvailabilityQueryContainer::CONCRETE_SKU],
                AvailabilityQueryContainer::CONCRETE_NAME => $productItem[AvailabilityQueryContainer::CONCRETE_NAME],
                AvailabilityQueryContainer::CONCRETE_AVAILABILITY => $productItem[AvailabilityQueryContainer::CONCRETE_AVAILABILITY],
                AvailabilityQueryContainer::STOCK_QUANTITY => $productItem[AvailabilityQueryContainer::STOCK_QUANTITY],
                AvailabilityQueryContainer::RESERVATION_QUANTITY => $productItem[AvailabilityQueryContainer::RESERVATION_QUANTITY] ?: 0,
                self::TABLE_COL_ACTION => $this->createEditButton($productItem),
            ];
        }

        return $result;
    }

    /**
     * @param \Orm\Zed\Availability\Persistence\SpyAvailability $productAbstractEntity
     *
     * @return string
     */
    protected function createEditButton(SpyAvailability $productAbstractEntity)
    {
        $viewTaxSetUrl = Url::generate(
            '/availability/index/edit',
            [
                self::URL_PARAM_ID_PRODUCT => $productAbstractEntity[AvailabilityQueryContainer::ID_PRODUCT],
                self::URL_PARAM_SKU => $productAbstractEntity[AvailabilityQueryContainer::CONCRETE_SKU],
                self::URL_PARAM_ID_PRODUCT_ABSTRACT => $this->idProductAbstract
            ]
        );
        return $this->generateEditButton($viewTaxSetUrl, 'Edit Stock');
    }

}
