<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Ratepay\Business\Status;

use Generated\Shared\Transfer\OrderTransfer;
use Orm\Zed\Ratepay\Persistence\Base\SpyPaymentRatepayLog;
use Spryker\Zed\Ratepay\Business\Api\Constants as ApiConstants;
use \Spryker\Zed\Ratepay\Persistence\RatepayQueryContainerInterface;

class TransactionStatus implements TransactionStatusInterface
{

    /**
     * @var \Spryker\Zed\Ratepay\Persistence\RatepayQueryContainerInterface $queryContainer
     */
    protected $queryContainer;

    /**
     * @param \Spryker\Zed\Ratepay\Persistence\RatepayQueryContainerInterface $queryContainer
     *
     */
    public function __construct(
        RatepayQueryContainerInterface $queryContainer
    ) {
        $this->queryContainer = $queryContainer;
    }

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return \Orm\Zed\Ratepay\Persistence\SpyPaymentRatepay
     */
    protected function loadOrderPayment(OrderTransfer $orderTransfer)
    {
        return $this->queryContainer
            ->queryPayments()
            ->findByFkSalesOrder(
                $orderTransfer->requireIdSalesOrder()->getIdSalesOrder()
            )->getFirst();
    }

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return bool
     */
    public function isPaymentRequestSuccess(OrderTransfer $orderTransfer)
    {
        /** @var SpyPaymentRatepayLog $paymentLog */
        $paymentLog = $this->queryContainer
            ->queryPaymentLogQueryBySalesOrderId($orderTransfer->requireIdSalesOrder()->getIdSalesOrder())
            ->filterByMessage(ApiConstants::REQUEST_MODEL_PAYMENT_REQUEST)
            ->find()
            ->getLast()
        ;
        if (!$paymentLog) {
            return false;
        }

        return in_array(
            $paymentLog->getResponseResultCode(),
            [
                ApiConstants::REQUEST_CODE_SUCCESS_MATRIX[ApiConstants::REQUEST_MODEL_PAYMENT_REQUEST],
            ]
        );
    }

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return bool
     */
    public function isPaymentConfirmed(OrderTransfer $orderTransfer)
    {
        $payment = $this->loadOrderPayment($orderTransfer);
        return in_array(
            $payment->getResultCode(),
            [
                ApiConstants::REQUEST_CODE_SUCCESS_MATRIX[ApiConstants::REQUEST_MODEL_PAYMENT_CONFIRM],
                ApiConstants::REQUEST_CODE_SUCCESS_MATRIX[ApiConstants::REQUEST_MODEL_DELIVER_CONFIRM],
            ]
        );
    }

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return bool
     */
    public function isDeliveryConfirmed(OrderTransfer $orderTransfer)
    {
        $payment = $this->loadOrderPayment($orderTransfer);
        return in_array(
            $payment->getResultCode(),
            [
                ApiConstants::REQUEST_CODE_SUCCESS_MATRIX[ApiConstants::REQUEST_MODEL_DELIVER_CONFIRM],
            ]
        );
    }

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return bool
     */
    public function isCancellationConfirmed(OrderTransfer $orderTransfer)
    {
        $payment = $this->loadOrderPayment($orderTransfer);
        return in_array(
            $payment->getResultCode(),
            [
                ApiConstants::REQUEST_CODE_SUCCESS_MATRIX[ApiConstants::REQUEST_MODEL_PAYMENT_CHANGE],
            ]
        );
    }

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return bool
     */
    public function isRefundApproved(OrderTransfer $orderTransfer)
    {
        $payment = $this->loadOrderPayment($orderTransfer);
        return in_array(
            $payment->getResultCode(),
            [
                ApiConstants::REQUEST_CODE_SUCCESS_MATRIX[ApiConstants::REQUEST_MODEL_PAYMENT_CHANGE],
            ]
        );
    }

}
