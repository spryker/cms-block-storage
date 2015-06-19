<?php

namespace SprykerFeature\Zed\Distributor\Persistence;

use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\Exception\PropelException;
use SprykerEngine\Zed\Kernel\Persistence\AbstractQueryContainer;
use SprykerFeature\Zed\Distributor\Persistence\Propel\Map\SpyDistributorItemTypeTableMap;
use SprykerFeature\Zed\Distributor\Persistence\Propel\Map\SpyDistributorReceiverTableMap;
use SprykerFeature\Zed\Distributor\Persistence\Propel\SpyDistributorItemQuery;
use SprykerFeature\Zed\Distributor\Persistence\Propel\SpyDistributorItemTypeQuery;
use SprykerFeature\Zed\Distributor\Persistence\Propel\SpyDistributorReceiverQuery;
use SprykerFeature\Zed\Library\Propel\Formatter\PropelArraySetFormatter;

class DistributorQueryContainer extends AbstractQueryContainer implements
    DistributorQueryContainerInterface
{
    /**
     * @return ModelCriteria
     */
    public function queryItemTypes()
    {
        $query = SpyDistributorItemTypeQuery::create()
            ->addSelectColumn(SpyDistributorItemTypeTableMap::COL_KEY)
            ->setDistinct()
            ->setFormatter(new PropelArraySetFormatter())
        ;

        return $query;
    }

    /**
     * @param string $typeKey
     *
     * @return $this|SpyDistributorItemTypeQuery
     */
    public function queryTypeByKey($typeKey)
    {
        $query = SpyDistributorItemTypeQuery::create()
            ->filterByKey($typeKey)
        ;

        return $query;
    }

    /**
     * @param string $typeKey
     * @param string $timestamp
     *
     * @throws PropelException
     * @return SpyDistributorItemQuery
     */
    public function queryTouchedItemsByTypeKey($typeKey, $timestamp)
    {
        return SpyDistributorItemQuery::create()
            ->filterByTouched(['min' => $timestamp])
            ->useSpyDistributorItemTypeQuery()
            ->filterByKey($typeKey)
            ->endUse()
        ;
    }

    /**
     * @return $this|ModelCriteria
     */
    public function queryReceivers()
    {
        $query = SpyDistributorReceiverQuery::create()
            ->addSelectColumn(SpyDistributorReceiverTableMap::COL_KEY)
            ->setDistinct()
            ->setFormatter(new PropelArraySetFormatter())
        ;

        return $query;
    }

    /**
     * @param string $itemType
     * @param string $idItem
     *
     * @return SpyDistributorItemQuery
     */
    public function queryItemByTypeAndId($itemType, $idItem)
    {
        $foreignKeyColumn = $this->getForeignKeyColumnByType($itemType);

        $query = SpyDistributorItemQuery::create()
            ->filterBy($foreignKeyColumn, $idItem)
            ->useSpyDistributorItemTypeQuery()
            ->filterByKey($itemType)
            ->endUse()
        ;

        return $query;
    }

    /**
     * @param $itemType
     *
     * @return string
     */
    protected function getForeignKeyColumnByType($itemType)
    {
        return 'fk_' . $itemType;
    }
}
