<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Braintree\Business\Payment\Handler\Transaction;

use Generated\Shared\Transfer\BraintreeTransactionResponseTransfer;
use Generated\Shared\Transfer\OrderTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Orm\Zed\Braintree\Persistence\SpyPaymentBraintreeTransactionRequestLog;
use Orm\Zed\Braintree\Persistence\SpyPaymentBraintreeTransactionStatusLog;
use Spryker\Shared\Braintree\BraintreeConstants;
use Spryker\Shared\Config\Config;
use Spryker\Zed\Braintree\BraintreeConfig;
use Spryker\Zed\Payolution\Business\Api\Adapter\AdapterInterface;
use Spryker\Zed\Braintree\Business\Payment\Handler\AbstractPaymentHandler;
use Spryker\Zed\Braintree\Business\Payment\Method\ApiConstants;
use Spryker\Zed\Braintree\Persistence\BraintreeQueryContainerInterface;

class Transaction extends AbstractPaymentHandler implements TransactionInterface
{

    /**
     * @var \Spryker\Zed\Braintree\Persistence\BraintreeQueryContainerInterface
     */
    protected $queryContainer;

    /**
     * @param \Spryker\Zed\Payolution\Business\Api\Adapter\AdapterInterface $executionAdapter
     * @param \Spryker\Zed\Braintree\Persistence\BraintreeQueryContainerInterface $queryContainer
     * @param \Spryker\Zed\Braintree\BraintreeConfig $config
     */
    public function __construct(
        AdapterInterface $executionAdapter,
        BraintreeQueryContainerInterface $queryContainer,
        BraintreeConfig $config
    ) {
        parent::__construct(
            $executionAdapter,
            $config
        );

        $this->queryContainer = $queryContainer;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\BraintreeTransactionResponseTransfer
     */
    public function preCheckPayment(QuoteTransfer $quoteTransfer)
    {
        $paymentTransfer = $quoteTransfer->getPayment()->getBraintree();

        $this->initializeBrainTree();

        $response = \Braintree\Transaction::sale([
            'amount' => $quoteTransfer->getTotals()->getGrandTotal() / 100,
            'paymentMethodNonce' => $paymentTransfer->getNonce(),
        ]);

        $responseTransfer = new BraintreeTransactionResponseTransfer();
        $responseTransfer->setIsSuccess($response->success);

        if (!$response->success) {
            $paymentTransfer->setNonce('');
            $responseTransfer->setMessage($response->message);
            return $responseTransfer;
        }

        /** @var \Braintree\Transaction $transaction */
        $transaction = $response->transaction;
        $paymentTransfer->setPaymentType($transaction->paymentInstrumentType);

        $responseTransfer->setCode($transaction->processorSettlementResponseCode);
        $responseTransfer->setTransactionId($transaction->id);
        $responseTransfer->setTransactionStatus($transaction->status);
        $responseTransfer->setTransactionType($transaction->type);
        $responseTransfer->setMerchantAccount($transaction->merchantAccountId);
        $responseTransfer->setCreditCardType($transaction->creditCardDetails->cardType);
        $responseTransfer->setPaymentType($transaction->paymentInstrumentType);

        return $responseTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     * @param int $idPayment
     *
     * @return \Generated\Shared\Transfer\BraintreeTransactionResponseTransfer
     */
    public function authorizePayment(OrderTransfer $orderTransfer, $idPayment)
    {
        $paymentEntity = $this->getPaymentEntity($idPayment);
        //$methodMapper = $this->getMethodMapper($paymentEntity->getAccountBrand());

        $this->initializeBrainTree();
        $this->logApiRequest($paymentEntity->getTransactionId(), ApiConstants::PAYMENT_CODE_AUTHORIZE, $idPayment);

        $transaction = \Braintree\Transaction::find($paymentEntity->getTransactionId());
        file_put_contents('xxx_auth.log', print_r($transaction, true));

        $responseTransfer = new BraintreeTransactionResponseTransfer();
        $responseTransfer->setTransactionId($paymentEntity->getTransactionId());
        $isSuccess = $transaction->processorResponseCode === ApiConstants::PAYMENT_CODE_AUTHORIZE_SUCCESS;
        $responseTransfer->setIsSuccess($isSuccess);

        if (!$isSuccess) {
            $responseTransfer->setMessage('Could not find payment with the transaction id ' . $paymentEntity->getTransactionId());
            $this->logApiResponse($responseTransfer, $idPayment);

            return $responseTransfer;
        }

        $responseTransfer->setProcessingTimestamp($transaction->createdAt);
        $responseTransfer->setTransactionStatus($transaction->status);
        $responseTransfer->setTransactionType($transaction->type);
        $responseTransfer->setTransactionAmount($transaction->amount);
        $responseTransfer->setMerchantAccount($transaction->merchantAccountId);
        $this->logApiResponse($responseTransfer, $idPayment, $transaction->statusHistory);

        return $responseTransfer;


        /*
        $customerTransfer = $orderTransfer->getCustomer();
        $customerId = \Braintree\Customer::create([
            'firstName' => $customerTransfer->getFirstName(),
            'lastName' => $customerTransfer->getLastName(),
            'company' => $customerTransfer->getCompany(),
            'email' => $customerTransfer->getEmail(),
            //'phone' => '281.330.8004',
            //'fax' => '419.555.1235',
            //'website' => 'http://example.com'
        ]);

        $customerId = $customerId->customer->id;
        file_put_contents('xxx3.log', print_r($customerId, true));


        $result = \Braintree\PaymentMethod::create([
            'customerId' => $customerId,
            'paymentMethodNonce' => $paymentEntity->getNonce()
        ]);
        $token = $result->paymentMethod->token;
        file_put_contents('xxx4.log', print_r($token, true));

        */
    }

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     * @param int $idPayment
     *
     * @return \Generated\Shared\Transfer\BraintreeTransactionResponseTransfer
     */
    public function revertPayment(OrderTransfer $orderTransfer, $idPayment)
    {
        $paymentEntity = $this->getPaymentEntity($idPayment);
        $statusLogEntity = $this->getLatestTransactionStatusLogItem($idPayment);

        $this->initializeBrainTree();
        $this->logApiRequest($paymentEntity->getTransactionId(), ApiConstants::PAYMENT_CODE_REVERSAL, $idPayment);

        // For status of authorized or submittedForSettlement
        $response = \Braintree\Transaction::void($paymentEntity->getTransactionId());
        file_put_contents('xxx_void.log', print_r($response, true));

        $responseTransfer = new BraintreeTransactionResponseTransfer();
        $responseTransfer->setTransactionId($paymentEntity->getTransactionId());
        $responseTransfer->setIsSuccess($response->success);

        if (!$response->success) {
            $responseTransfer->setMessage($response->message);
            $this->logApiResponse($responseTransfer, $idPayment);

            return $responseTransfer;
        }

        $responseTransfer->setProcessingTimestamp($response->transaction->createdAt);
        $responseTransfer->setTransactionStatus($response->transaction->status);
        $responseTransfer->setTransactionType($response->transaction->type);
        $responseTransfer->setTransactionAmount($response->transaction->amount);
        $responseTransfer->setMerchantAccount($response->transaction->merchantAccountId);
        $this->logApiResponse($responseTransfer, $idPayment, $response->transaction->statusHistory);

        return $responseTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     * @param int $idPayment
     *
     * @return \Generated\Shared\Transfer\BraintreeTransactionResponseTransfer
     */
    public function capturePayment(OrderTransfer $orderTransfer, $idPayment)
    {
        $paymentEntity = $this->getPaymentEntity($idPayment);
        //$statusLogEntity = $this->getLatestTransactionStatusLogItem($idPayment);

        $transactionId = $paymentEntity->getTransactionId();

        $this->initializeBrainTree();
        $this->logApiRequest($paymentEntity->getTransactionId(), ApiConstants::PAYMENT_CODE_CAPTURE, $idPayment);

        $response = \Braintree\Transaction::submitForSettlement($transactionId);
        file_put_contents('xxx_capture.log', print_r($response, true));

        $responseTransfer = new BraintreeTransactionResponseTransfer();
        $responseTransfer->setTransactionId($paymentEntity->getTransactionId());
        $responseTransfer->setIsSuccess($response->success);

        if (!$response->success) {
            $responseTransfer->setMessage($response->message);
            $this->logApiResponse($responseTransfer, $idPayment);

            return $responseTransfer;
        }

        $responseTransfer->setProcessingTimestamp($response->transaction->createdAt);
        $responseTransfer->setTransactionStatus($response->transaction->status);
        $responseTransfer->setTransactionType($response->transaction->type);
        $responseTransfer->setTransactionAmount($response->transaction->amount);
        $responseTransfer->setMerchantAccount($response->transaction->merchantAccountId);
        $this->logApiResponse($responseTransfer, $idPayment, $response->transaction->statusHistory);

        return $responseTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     * @param int $idPayment
     *
     * @return \Generated\Shared\Transfer\BraintreeTransactionResponseTransfer
     */
    public function refundPayment(OrderTransfer $orderTransfer, $idPayment)
    {
        $paymentEntity = $this->getPaymentEntity($idPayment);
        //$statusLogEntity = $this->getLatestTransactionStatusLogItem($idPayment);

        $transactionId = $paymentEntity->getTransactionId();

        $this->initializeBrainTree();
        $this->logApiRequest($paymentEntity->getTransactionId(), ApiConstants::PAYMENT_CODE_REFUND, $idPayment);

        $response = \Braintree\Transaction::refund($transactionId);
        file_put_contents('xxx_refund.log', print_r($response, true));

        $responseTransfer = new BraintreeTransactionResponseTransfer();
        $responseTransfer->setTransactionId($paymentEntity->getTransactionId());
        $responseTransfer->setIsSuccess($response->success);

        if (!$response->success) {
            $responseTransfer->setMessage($response->message);
            $this->logApiResponse($responseTransfer, $idPayment);

            return $responseTransfer;
        }

        $responseTransfer->setProcessingTimestamp($response->transaction->createdAt);
        $responseTransfer->setTransactionStatus($response->transaction->status);
        $responseTransfer->setTransactionType($response->transaction->type);
        $responseTransfer->setTransactionAmount($response->transaction->amount);
        $responseTransfer->setMerchantAccount($response->transaction->merchantAccountId);
        $this->logApiResponse($responseTransfer, $idPayment, $response->transaction->statusHistory);

        return $responseTransfer;
    }

    /**
     * @param int $idPayment
     *
     * @return \Orm\Zed\Braintree\Persistence\SpyPaymentBraintree
     */
    protected function getPaymentEntity($idPayment)
    {
        return $this->queryContainer->queryPaymentById($idPayment)->findOne();
    }

    /**
     * @param int $idPayment
     *
     * @return \Orm\Zed\Braintree\Persistence\SpyPaymentBraintreeTransactionStatusLog
     */
    protected function getLatestTransactionStatusLogItem($idPayment)
    {
        return $this
            ->queryContainer
            ->queryTransactionStatusLogByPaymentIdLatestFirst($idPayment)
            ->findOne();
    }

    /**
     * @param string $code
     * @param int $idPayment
     *
     * @return \Orm\Zed\Braintree\Persistence\SpyPaymentBraintreeTransactionRequestLog
     */
    protected function logApiRequest($transactionId, $code, $idPayment)
    {
        $logEntity = new SpyPaymentBraintreeTransactionRequestLog();
        $logEntity
            ->setTransactionId($transactionId)
            ->setTransactionCode($code)
            ->setFkPaymentBraintree($idPayment);
        $logEntity->save();

        return $logEntity;
    }

    /**
     * @param \Generated\Shared\Transfer\BraintreeTransactionResponseTransfer $responseTransfer
     * @param int $idPayment
     * @param array $logs
     *
     * @return void
     */
    protected function logApiResponse(BraintreeTransactionResponseTransfer $responseTransfer, $idPayment, array $logs = [])
    {
        if (count($logs) > 0) {
            $log = array_pop($logs);
            $responseTransfer->setTransactionStatus($log->status);
            $responseTransfer->setTransactionAmount($log->amount);
            $responseTransfer->setProcessingTimestamp($log->timestamp->getTimestamp());
        }

        $logEntity = new SpyPaymentBraintreeTransactionStatusLog();
        $logEntity->fromArray($responseTransfer->toArray());
        $logEntity->setFkPaymentBraintree($idPayment);
        $logEntity->save();
    }

    /**
     * @return void
     */
    protected function initializeBrainTree()
    {
        $environment = Config::get(BraintreeConstants::ENVIRONMENT);
        $merchantId = Config::get(BraintreeConstants::MERCHANT_ID);
        $publicKey = Config::get(BraintreeConstants::PUBLIC_KEY);
        $privateKey = Config::get(BraintreeConstants::PRIVATE_KEY);
        \Braintree\Configuration::environment($environment);
        \Braintree\Configuration::merchantId($merchantId);
        \Braintree\Configuration::publicKey($publicKey);
        \Braintree\Configuration::privateKey($privateKey);
    }

}
