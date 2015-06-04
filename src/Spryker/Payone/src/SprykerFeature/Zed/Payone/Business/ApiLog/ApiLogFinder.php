<?php

namespace SprykerFeature\Zed\Payone\Business\ApiLog;

use Generated\Shared\Payone\PayonePaymentInterface;
use Generated\Shared\Transfer\AuthorizationCheckResponseTransfer;
use Generated\Shared\Payone\ApiCallResponseCheckInterface;
use SprykerFeature\Zed\Payone\Persistence\PayoneQueryContainerInterface;
use SprykerFeature\Shared\Payone\PayoneApiConstants;
use SprykerFeature\Zed\Payone\Persistence\Propel\SpyPaymentPayone;
use SprykerFeature\Zed\Payone\Persistence\Propel\SpyPaymentPayoneApiLog;

/**
 * @todo R E F A C T O R - Horrorcode
 */
class ApiLogFinder
{

    /**
     * @var PayoneQueryContainerInterface
     */
    private $queryContainer;

    /**
     * @param PayoneQueryContainerInterface $queryContainer
     */
    public function __construct(PayoneQueryContainerInterface $queryContainer)
    {
        $this->queryContainer = $queryContainer;
    }

    /**
     * @todo R E F A C T O R - Horrorcode - Only thougts of what could happen - Used by oms conditions
     *
     * @param PayonePaymentInterface $payment
     * @return bool
     */
    public function isAuthorizationSuccessful(PayonePaymentInterface $payment)
    {
        // no payment - no success - but: what if auth call was successfull but
        // exception occurred....?
        // @todo We should get order id it to load via fk_order in payment instead of transaction
        $paymentEntity = $this->findPayment($payment->getTransactionId());
        if (!$paymentEntity) {
            return false;
        }

        // no transaction id means it was not updated cause payment failed (or exception after call...)
        // but we should/may have api log with response
        if (!$paymentEntity->getTransactionId()) {
            return false;
        }

        // we have transaction id, check api log. if we do not have api log entity we can not
        // say if it is a redirect! could check payment method.. cc/paypal/ideal... is dirty
        // what to do in this case
        $apiLogEntity = $this->findApiLog($paymentEntity, $payment->getAuthorizationType());
        if (!$apiLogEntity) {
            return false;
        }

        // we have transaction id in payment and api log
        // authorization is success on APPROVED and REDIRECT
        $status = $apiLogEntity->getStatus();
        if ($status === PayoneApiConstants::RESPONSE_TYPE_APPROVED ||
            $status === PayoneApiConstants::RESPONSE_TYPE_REDIRECT) {
            return true;
        }

        return false;
    }

    /**
     * @todo R E F A C T O R - Horrorcode
     * @param PayonePaymentInterface $payment
     * @return AuthorizationCheckResponseTransfer
     */
    public function getAuthorizationResponse(PayonePaymentInterface $payment)
    {
        $response = new AuthorizationCheckResponseTransfer();

        $paymentEntity = $this->findPayment($payment->getTransactionId());
        // no payment - no success - but: what if auth call was successfull but
        // exception occurred....?
        if (!$paymentEntity) {
            $response->setIsSuccess(false);
            // @todo define customer display message
            //$response->setCustomerErrorMessage($customerErrorMessage);

            return $response;
        }

        $apiLogEntity = $this->findApiLog($paymentEntity, $payment->getAuthorizationType());

        // no transaction id means it was not updated cause payment failed (or exception after call...)
        // but we should/may have api log with response
        if (!$paymentEntity->getTransactionId()) {
            $response->setIsSuccess(false);
            if ($apiLogEntity) {
                $response->setCustomerErrorMessage($apiLogEntity->getErrorMessageUser());
            } else {
                // @todo define customer display message
                //$response->setCustomerErrorMessage($customerErrorMessage);
            }

            return $response;
        }

        // we have transaction id, check api log. if we do not have api log entity we can not
        // say if it is a redirect! could check payment method.. cc/paypal/ideal... is dirty
        // what to do in this case
        if (!$apiLogEntity) {
            $response->setIsSuccess(false);
            // @todo define customer display message
            //$response->setCustomerErrorMessage($customerErrorMessage);

            return $response;
        }

        // we have transaction id in payment and api log
        if (
            // authorization is success on APPROVED and REDIRECT
            $apiLogEntity->getStatus() === PayoneApiConstants::RESPONSE_TYPE_APPROVED ||
            $apiLogEntity->getStatus() === PayoneApiConstants::RESPONSE_TYPE_REDIRECT
        ) {
            $response->setIsSuccess(true);
            $response->setRequest($apiLogEntity->getRequest());

            if ($apiLogEntity->getStatus() === PayoneApiConstants::RESPONSE_TYPE_REDIRECT) {
                $response->setIsRedirect(true);
                $response->setRedirectUrl($apiLogEntity->getRedirectUrl());
            }
        } else {
            $response->setIsSuccess(false);
            $response->setErrorCode($apiLogEntity->getErrorCode());
            $response->setCustomerErrorMessage($apiLogEntity->getErrorMessageUser());
            $response->setInternalErrorMessage($apiLogEntity->getErrorMessageInternal());
        }

        return $response;
    }

    /**
     * @param int $transactionId
     * @return SpyPaymentPayone|null
     */
    protected function findPayment($transactionId)
    {
        return $this->queryContainer->getPaymentByTransactionIdQuery($transactionId)->findOne();
    }

    /**
     * @param SpyPaymentPayone $payment
     * @param string $authorizationType
     * @return SpyPaymentPayoneApiLog
     */
    protected function findApiLog(SpyPaymentPayone $payment, $authorizationType)
    {
        return $this->queryContainer->getApiLogByPaymentAndRequestTypeQuery(
            $payment->getPrimaryKey(),
            $authorizationType
        )->findOne();
    }

    /**
     * @param ApiCallResponseCheckInterface $apiCallCheck
     * @return bool
     */
    public function isApiCallSuccessful(ApiCallResponseCheckInterface $apiCallCheck)
    {
        // @todo
    }

}
