<?php

/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace Spryker\Zed\Payone\Business\Payment\MethodMapper;

use Generated\Shared\Transfer\PayoneAuthorizationTransfer;
use Spryker\Zed\Payone\Business\Api\Request\Container\Authorization\AbstractAuthorizationContainer;
use Spryker\Zed\Payone\Business\Api\Request\Container\Authorization\PaymentMethod\PrepaymentContainer;
use Spryker\Zed\Payone\Business\Api\Request\Container\Authorization\PersonalContainer;
use Spryker\Zed\Payone\Business\Api\Request\Container\AuthorizationContainer;
use Spryker\Zed\Payone\Business\Api\Request\Container\CaptureContainer;
use Spryker\Zed\Payone\Business\Api\Request\Container\DebitContainer;
use Spryker\Zed\Payone\Business\Api\Request\Container\PreAuthorizationContainer;
use Spryker\Shared\Payone\PayoneApiConstants;
use Spryker\Zed\Payone\Business\Api\Request\Container\RefundContainer;
use Orm\Zed\Payone\Persistence\SpyPaymentPayone;

class Prepayment extends AbstractMapper
{

    /**
     * @return string
     */
    public function getName()
    {
        return PayoneApiConstants::PAYMENT_METHOD_PREPAYMENT;
    }

    /**
     * @param \Orm\Zed\Payone\Persistence\SpyPaymentPayone $paymentEntity
     *
     * @return \Spryker\Zed\Payone\Business\Api\Request\Container\AuthorizationContainer
     */
    public function mapPaymentToAuthorization(SpyPaymentPayone $paymentEntity)
    {
        $authorizationContainer = new AuthorizationContainer();
        $authorizationContainer = $this->mapPaymentToAbstractAuthorization($paymentEntity, $authorizationContainer);

        return $authorizationContainer;
    }

    /**
     * @param \Orm\Zed\Payone\Persistence\SpyPaymentPayone $paymentEntity
     *
     * @return \Spryker\Zed\Payone\Business\Api\Request\Container\CaptureContainer
     */
    public function mapPaymentToCapture(SpyPaymentPayone $paymentEntity)
    {
        $paymentDetailEntity = $paymentEntity->getSpyPaymentPayoneDetail();

        $captureContainer = new CaptureContainer();
        $captureContainer->setAmount($paymentDetailEntity->getAmount());
        $captureContainer->setCurrency($this->getStandardParameter()->getCurrency());
        $captureContainer->setTxid($paymentEntity->getTransactionId());

        return $captureContainer;
    }

    /**
     * @param \Orm\Zed\Payone\Persistence\SpyPaymentPayone $paymentEntity
     *
     * @return \Spryker\Zed\Payone\Business\Api\Request\Container\PreAuthorizationContainer
     */
    public function mapPaymentToPreAuthorization(SpyPaymentPayone $paymentEntity)
    {
        $preAuthorizationContainer = new PreAuthorizationContainer();
        $preAuthorizationContainer = $this->mapPaymentToAbstractAuthorization($paymentEntity, $preAuthorizationContainer);

        return $preAuthorizationContainer;
    }

    /**
     * @param \Orm\Zed\Payone\Persistence\SpyPaymentPayone $paymentEntity
     * @param \Spryker\Zed\Payone\Business\Api\Request\Container\Authorization\AbstractAuthorizationContainer $authorizationContainer
     *
     * @return \Spryker\Zed\Payone\Business\Api\Request\Container\Authorization\AbstractAuthorizationContainer
     */
    protected function mapPaymentToAbstractAuthorization(SpyPaymentPayone $paymentEntity, AbstractAuthorizationContainer $authorizationContainer)
    {
        $paymentDetailEntity = $paymentEntity->getSpyPaymentPayoneDetail();

        $authorizationContainer->setAid($this->getStandardParameter()->getAid());
        $authorizationContainer->setClearingType(PayoneApiConstants::CLEARING_TYPE_PREPAYMENT);
        $authorizationContainer->setReference($paymentEntity->getReference());
        $authorizationContainer->setAmount($paymentDetailEntity->getAmount());
        $authorizationContainer->setCurrency($this->getStandardParameter()->getCurrency());
        $authorizationContainer->setPaymentMethod($this->createPaymentMethodContainerFromPayment($paymentEntity));

        $billingAddressEntity = $paymentEntity->getSpySalesOrder()->getBillingAddress();

        $personalContainer = new PersonalContainer();
        $this->mapBillingAddressToPersonalContainer($personalContainer, $billingAddressEntity);

        $authorizationContainer->setPersonalData($personalContainer);

        return $authorizationContainer;
    }

    /**
     * @param \Orm\Zed\Payone\Persistence\SpyPaymentPayone $paymentEntity
     *
     * @return \Spryker\Zed\Payone\Business\Api\Request\Container\DebitContainer
     */
    public function mapPaymentToDebit(SpyPaymentPayone $paymentEntity)
    {
        $debitContainer = new DebitContainer();

        $debitContainer->setTxid($paymentEntity->getTransactionId());
        $debitContainer->setSequenceNumber($this->getNextSequenceNumber($paymentEntity->getTransactionId()));
        $debitContainer->setCurrency($this->getStandardParameter()->getCurrency());
        $debitContainer->setAmount($paymentEntity->getSpyPaymentPayoneDetail()->getAmount());

        return $debitContainer;
    }

    /**
     * @param \Orm\Zed\Payone\Persistence\SpyPaymentPayone $paymentEntity
     *
     * @return \Spryker\Zed\Payone\Business\Api\Request\Container\RefundContainer
     */
    public function mapPaymentToRefund(SpyPaymentPayone $paymentEntity)
    {
        $refundContainer = new RefundContainer();

        $refundContainer->setTxid($paymentEntity->getTransactionId());
        $refundContainer->setSequenceNumber($this->getNextSequenceNumber($paymentEntity->getTransactionId()));
        $refundContainer->setCurrency($this->getStandardParameter()->getCurrency());

        $refundContainer->setBankcountry($paymentEntity->getSpyPaymentPayoneDetail()->getBankCountry());
        $refundContainer->setBankaccount($paymentEntity->getSpyPaymentPayoneDetail()->getBankAccount());
        $refundContainer->setBankcode($paymentEntity->getSpyPaymentPayoneDetail()->getBankCode());
        $refundContainer->setBankbranchcode($paymentEntity->getSpyPaymentPayoneDetail()->getBankBranchCode());
        $refundContainer->setBankcheckdigit($paymentEntity->getSpyPaymentPayoneDetail()->getBankCheckDigit());
        $refundContainer->setIban($paymentEntity->getSpyPaymentPayoneDetail()->getIban());
        $refundContainer->setBic($paymentEntity->getSpyPaymentPayoneDetail()->getBic());

        return $refundContainer;
    }

    /**
     * @param \Orm\Zed\Payone\Persistence\SpyPaymentPayone $paymentEntity
     *
     * @return \Spryker\Zed\Payone\Business\Api\Request\Container\Authorization\PaymentMethod\CreditCardPseudoContainer
     */
    protected function createPaymentMethodContainerFromPayment(SpyPaymentPayone $paymentEntity)
    {
        $paymentMethodContainer = new PrepaymentContainer();

        return $paymentMethodContainer;
    }

    /**
     * @param \Generated\Shared\Transfer\PayoneAuthorizationTransfer $payoneAuthorizationTransfer
     *
     * @return \Spryker\Zed\Payone\Business\Api\Request\Container\Authorization\PersonalContainer
     */
    protected function createAuthorizationPersonalData(PayoneAuthorizationTransfer $payoneAuthorizationTransfer)
    {
        $personalContainer = new PersonalContainer();

        $personalContainer->setFirstName($payoneAuthorizationTransfer->getOrder()->getFirstName());
        $personalContainer->setLastName($payoneAuthorizationTransfer->getOrder()->getLastName());
        $personalContainer->setCountry($this->storeConfig->getCurrentCountry());

        return $personalContainer;
    }

}
