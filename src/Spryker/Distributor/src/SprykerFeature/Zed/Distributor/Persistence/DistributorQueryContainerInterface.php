<?php

namespace SprykerFeature\Zed\Distributor\Persistence;

use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\Exception\PropelException;
use SprykerFeature\Zed\Distributor\Persistence\Propel\SpyDistributorItemQuery;
use SprykerFeature\Zed\Distributor\Persistence\Propel\SpyDistributorItemTypeQuery;

interface DistributorQueryContainerInterface
{
    /**
     * @return ModelCriteria
     */
    public function queryItemTypes();

    /**
     * @param string $typeKey
     *
     * @return $this|SpyDistributorItemTypeQuery
     */
    public function queryTypeByKey($typeKey);

    /**
     * @param string $typeKey
     * @param string $timestamp
     *
     * @throws PropelException
     * @return SpyDistributorItemQuery
     */
    public function queryTouchedItemsByTypeKey($typeKey, $timestamp);

    /**
     * @return $this|ModelCriteria
     */
    public function queryReceivers();

    /**
     * @param string $itemType
     * @param int $idItem
     *
     * @return SpyDistributorItemQuery
     */
    public function queryItemByTypeAndId($itemType, $idItem);
}
