<?php

/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace Spryker\Zed\Tax\Persistence;

use Spryker\Zed\Kernel\Persistence\AbstractQueryContainer;
use Orm\Zed\Tax\Persistence\Map\SpyTaxRateTableMap;
use Orm\Zed\Tax\Persistence\Map\SpyTaxSetTableMap;
use Orm\Zed\Tax\Persistence\Map\SpyTaxSetTaxTableMap;
use Orm\Zed\Tax\Persistence\SpyTaxRateQuery;
use Orm\Zed\Tax\Persistence\SpyTaxSetQuery;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;

class TaxQueryContainer extends AbstractQueryContainer implements TaxQueryContainerInterface
{

    /**
     * @param int $id
     *
     * @return SpyTaxRateQuery
     */
    public function queryTaxRate($id)
    {
        return SpyTaxRateQuery::create()->filterByIdTaxRate($id);
    }

    /**
     * @return SpyTaxRateQuery
     */
    public function queryAllTaxRates()
    {
        return SpyTaxRateQuery::create();
    }

    /**
     * @param int $id
     *
     * @return SpyTaxSetQuery
     */
    public function queryTaxSet($id)
    {
        return SpyTaxSetQuery::create()->filterByIdTaxSet($id);
    }

    /**
     * @return SpyTaxSetQuery
     */
    public function queryAllTaxSets()
    {
        return SpyTaxSetQuery::create();
    }

    /**
     * @param ModelCriteria $expandableQuery
     *
     * @return self
     */
    public function joinTaxRates(ModelCriteria $expandableQuery)
    {
        $expandableQuery
            ->addJoin(
                SpyTaxSetTableMap::COL_ID_TAX_SET,
                SpyTaxSetTaxTableMap::COL_FK_TAX_SET,
                Criteria::LEFT_JOIN // @TODO Change to Criteria::INNER_JOIN as soon as there is a Tax GUI/Importer in Zed
            )
            ->addJoin(
                SpyTaxSetTaxTableMap::COL_FK_TAX_RATE,
                SpyTaxRateTableMap::COL_ID_TAX_RATE,
                Criteria::LEFT_JOIN // @TODO Change to Criteria::INNER_JOIN as soon as there is a Tax GUI/Importer in Zed
            );

        $expandableQuery
            ->withColumn(
                'GROUP_CONCAT(DISTINCT ' . SpyTaxRateTableMap::COL_NAME . ')',
                'tax_rate_names'
            )
            ->withColumn(
                'GROUP_CONCAT(DISTINCT ' . SpyTaxRateTableMap::COL_RATE . ')',
                'tax_rate_rates'
            );

        return $this;
    }

}
