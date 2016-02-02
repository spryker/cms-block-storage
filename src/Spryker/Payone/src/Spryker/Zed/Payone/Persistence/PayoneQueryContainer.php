<?php

/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace Spryker\Zed\Payone\Persistence;

use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\Collection\ObjectCollection;
use Orm\Zed\Payone\Persistence\Base\SpyPaymentPayoneQuery;
use Spryker\Zed\Kernel\Persistence\AbstractQueryContainer;
use Orm\Zed\Payone\Persistence\SpyPaymentPayoneApiLog;
use Orm\Zed\Payone\Persistence\SpyPaymentPayoneTransactionStatusLog;
use Orm\Zed\Payone\Persistence\SpyPaymentPayoneTransactionStatusLogOrderItem;
use Orm\Zed\Payone\Persistence\SpyPaymentPayoneTransactionStatusLogOrderItemQuery;
use Orm\Zed\Payone\Persistence\SpyPaymentPayoneTransactionStatusLogQuery;
use Orm\Zed\Payone\Persistence\SpyPaymentPayoneApiLogQuery;
use Orm\Zed\Sales\Persistence\SpySalesOrder;

class PayoneQueryContainer extends AbstractQueryContainer implements PayoneQueryContainerInterface
{

    /**
     * @todo CD-427 Follow naming conventions and use method name starting with 'query*'
     *
     * @param int $transactionId
     *
     * @return \Orm\Zed\Payone\Persistence\SpyPaymentPayoneTransactionStatusLogQuery
     */
    public function getCurrentSequenceNumberQuery($transactionId)
    {
        $query = SpyPaymentPayoneTransactionStatusLogQuery::create();
        $query->filterByTransactionId($transactionId)
              ->orderBySequenceNumber(Criteria::DESC);

        return $query;
    }

    /**
     * @todo CD-427 Follow naming conventions and use method name starting with 'query*'
     *
     * @param int $transactionId
     *
     * @return \Orm\Zed\Payone\Persistence\Base\SpyPaymentPayoneQuery
     */
    public function getPaymentByTransactionIdQuery($transactionId)
    {
        $query = SpyPaymentPayoneQuery::create();
        $query->filterByTransactionId($transactionId);

        return $query;
    }

    /**
     * @todo CD-427 Follow naming conventions and use method name starting with 'query*'
     *
     * @param int $fkPayment
     * @param string $requestName
     *
     * @return \Orm\Zed\Payone\Persistence\SpyPaymentPayoneApiLogQuery
     */
    public function getApiLogByPaymentAndRequestTypeQuery($fkPayment, $requestName)
    {
        $query = SpyPaymentPayoneApiLogQuery::create();
        $query->filterByFkPaymentPayone($fkPayment)
              ->filterByRequest($requestName);

        return $query;
    }

    /**
     * @todo CD-427 Follow naming conventions and use method name starting with 'query*'
     *
     * @param int $orderId
     *
     * @return \Orm\Zed\Payone\Persistence\Base\SpyPaymentPayoneQuery
     */
    public function getPaymentByOrderId($orderId)
    {
        $query = SpyPaymentPayoneQuery::create();
        $query->findByFkSalesOrder($orderId);

        return $query;
    }

    /**
     * @todo CD-427 Follow naming conventions and use method name starting with 'query*'
     *
     * @param int $orderId
     * @param string $request
     *
     * @return \Orm\Zed\Payone\Persistence\SpyPaymentPayoneApiLog
     */
    public function getApiLogsByOrderIdAndRequest($orderId, $request)
    {
        $query = SpyPaymentPayoneApiLogQuery::create()
            ->useSpyPaymentPayoneQuery()
            ->filterByFkSalesOrder($orderId)
            ->endUse()
            ->filterByRequest($request)
            ->orderByCreatedAt(Criteria::DESC) //TODO: Index?
            ->orderByIdPaymentPayoneApiLog(Criteria::DESC);

        return $query;
    }

    /**
     * @todo CD-427 Follow naming conventions and use method name starting with 'query*'
     *
     * @param int $paymentId
     *
     * @return \Orm\Zed\Payone\Persistence\Base\SpyPaymentPayoneQuery
     */
    public function getPaymentById($paymentId)
    {
        $query = SpyPaymentPayoneQuery::create();
        $query->findByFkSalesOrder($paymentId);

        return $query;
    }

    /**
     * @todo CD-427 Follow naming conventions and use method name starting with 'query*'
     *
     * @param int $idIdSalesOrder
     *
     * @return SpyPaymentPayoneTransactionStatusLog[]
     */
    public function getTransactionStatusLogsBySalesOrder($idIdSalesOrder)
    {
        $query = SpyPaymentPayoneTransactionStatusLogQuery::create()
            ->useSpyPaymentPayoneQuery()
            ->filterByFkSalesOrder($idIdSalesOrder)
            ->endUse()
            ->orderByCreatedAt();

        return $query;
    }

    /**
     * @todo CD-427 Follow naming conventions and use method name starting with 'query*'
     *
     * @param int $idSalesOrderItem
     * @param array $ids
     *
     * @return SpyPaymentPayoneTransactionStatusLogOrderItem[]
     */
    public function getTransactionStatusLogOrderItemsByLogIds($idSalesOrderItem, $ids)
    {
        $relations = SpyPaymentPayoneTransactionStatusLogOrderItemQuery::create()
            ->filterByIdPaymentPayoneTransactionStatusLog($ids)
            ->filterByIdSalesOrderItem($idSalesOrderItem);

        return $relations;
    }

    /**
     * @todo CD-427 Follow naming conventions and use method name starting with 'query*'
     *
     * @param int $idSalesOrder
     *
     * @return \Orm\Zed\Payone\Persistence\SpyPaymentPayoneApiLogQuery
     */
    public function getLastApiLogsByOrderId($idSalesOrder)
    {
        $query = SpyPaymentPayoneApiLogQuery::create()
            ->useSpyPaymentPayoneQuery()
            ->filterByFkSalesOrder($idSalesOrder)
            ->endUse()
            ->orderByCreatedAt(Criteria::DESC)
            ->orderByIdPaymentPayoneApiLog(Criteria::DESC);

        return $query;
    }

    /**
     * @todo CD-427 Follow naming conventions and use method name starting with 'query*'
     *
     * @param ObjectCollection $orders
     *
     * @return \Orm\Zed\Payone\Persistence\SpyPaymentPayoneApiLogQuery
     */
    public function getApiLogsByOrderIds($orders)
    {
        $ids = [];
        /** @var SpySalesOrder $order */
        foreach ($orders as $order) {
            $ids[] = $order->getIdSalesOrder();
        }

        $query = SpyPaymentPayoneApiLogQuery::create()
            ->useSpyPaymentPayoneQuery()
            ->filterByFkSalesOrder($ids)
            ->endUse()
            ->orderByCreatedAt();

        return $query;
    }

    /**
     * @todo CD-427 Follow naming conventions and use method name starting with 'query*'
     *
     * @param ObjectCollection $orders
     *
     * @return \Orm\Zed\Payone\Persistence\SpyPaymentPayoneTransactionStatusLogQuery
     */
    public function getTransactionStatusLogsByOrderIds($orders)
    {
        $ids = [];
        /** @var SpySalesOrder $order */
        foreach ($orders as $order) {
            $ids[] = $order->getIdSalesOrder();
        }

        $query = SpyPaymentPayoneTransactionStatusLogQuery::create()
            ->useSpyPaymentPayoneQuery()
            ->filterByFkSalesOrder($ids)
            ->endUse()
            ->orderByCreatedAt();

        return $query;
    }

}
