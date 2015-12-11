<?php

/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace SprykerFeature\Zed\Payone\Business\Api\Request\Container\Authorization;

use SprykerFeature\Zed\Payone\Business\Api\Request\Container\AbstractRequestContainer;
use SprykerFeature\Zed\Payone\Business\Api\Request\Container\Authorization\PaymentMethod\AbstractPaymentMethodContainer;
use SprykerFeature\Zed\Payone\Business\Api\Request\Container\AuthorizationContainerInterface;
use SprykerFeature\Zed\Payone\Business\Api\Request\Container\Invoicing\TransactionContainer;

abstract class AbstractAuthorizationContainer extends AbstractRequestContainer implements AuthorizationContainerInterface
{

    /**
     * Sub account ID
     *
     * @var int
     */
    protected $aid;

    /**
     * @var string
     */
    protected $clearingtype;

    /**
     * Merchant reference number for the payment process. (Permitted symbols: 0-9, a-z, A-Z, .,-,_,/)
     *
     * @var string
     */
    protected $reference;

    /**
     * Total amount (in smallest currency unit! e.g. cent)
     *
     * @var int
     */
    protected $amount;

    /**
     * Currency (ISO-4217)
     *
     * @var string
     */
    protected $currency;

    /**
     * Individual parameter
     *
     * @var string
     */
    protected $param;

    /**
     * dynamic text for debit and creditcard payments
     *
     * @var string
     */
    protected $narrative_text;

    /**
     * @var \SprykerFeature\Zed\Payone\Business\Api\Request\Container\Authorization\PersonalContainer
     */
    protected $personalData;

    /**
     * @var \SprykerFeature\Zed\Payone\Business\Api\Request\Container\Authorization\ShippingContainer
     */
    protected $shippingData;

    /**
     * @var \SprykerFeature\Zed\Payone\Business\Api\Request\Container\Authorization\PaymentMethod\AbstractPaymentMethodContainer
     */
    protected $paymentMethod;

    /**
     * @var \SprykerFeature\Zed\Payone\Business\Api\Request\Container\Authorization\ThreeDSecureContainer
     */
    protected $_3dsecure;

    /**
     * @var \SprykerFeature\Zed\Payone\Business\Api\Request\Container\Invoicing\TransactionContainer
     */
    protected $invoicing;

    /**
     * @var string
     */
    protected $onlinebanktransfertype;

    /**
     * @var string
     */
    protected $bankcountry;

    /**
     * @param int $aid
     *
     * @return void
     */
    public function setAid($aid)
    {
        $this->aid = $aid;
    }

    /**
     * @return int
     */
    public function getAid()
    {
        return $this->aid;
    }

    /**
     * @param int $amount
     *
     * @return void
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
    }

    /**
     * @return int
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param string $clearingtype
     *
     * @return void
     */
    public function setClearingType($clearingType)
    {
        $this->clearingtype = $clearingType;
    }

    /**
     * @return string
     */
    public function getClearingType()
    {
        return $this->clearingtype;
    }

    /**
     * @param string $currency
     *
     * @return void
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;
    }

    /**
     * @return string
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @param string $narrative_text
     *
     * @return void
     */
    public function setNarrativeText($narrative_text)
    {
        $this->narrative_text = $narrative_text;
    }

    /**
     * @return string
     */
    public function getNarrativeText()
    {
        return $this->narrative_text;
    }

    /**
     * @param string $param
     *
     * @return void
     */
    public function setParam($param)
    {
        $this->param = $param;
    }

    /**
     * @return string
     */
    public function getParam()
    {
        return $this->param;
    }

    /**
     * @param string $reference
     *
     * @return void
     */
    public function setReference($reference)
    {
        $this->reference = $reference;
    }

    /**
     * @return string
     */
    public function getReference()
    {
        return $this->reference;
    }

    /**
     * @param PersonalContainer $personalData
     *
     * @return void
     */
    public function setPersonalData(PersonalContainer $personalData)
    {
        $this->personalData = $personalData;
    }

    /**
     * @return PersonalContainer
     */
    public function getPersonalData()
    {
        return $this->personalData;
    }

    /**
     * @param ShippingContainer $delivery
     *
     * @return void
     */
    public function setShippingData(ShippingContainer $delivery)
    {
        $this->shippingData = $delivery;
    }

    /**
     * @return ShippingContainer
     */
    public function getShippingData()
    {
        return $this->shippingData;
    }

    /**
     * @param PaymentMethod\AbstractPaymentMethodContainer $paymentMethod
     *
     * @return void
     */
    public function setPaymentMethod(AbstractPaymentMethodContainer $paymentMethod)
    {
        $this->paymentMethod = $paymentMethod;
    }

    /**
     * @return PaymentMethod\AbstractPaymentMethodContainer
     */
    public function getPaymentMethod()
    {
        return $this->paymentMethod;
    }

    /**
     * @param ThreeDSecureContainer $secure
     *
     * @return void
     */
    public function set3dsecure(ThreeDSecureContainer $secure)
    {
        $this->_3dsecure = $secure;
    }

    /**
     * @return ThreeDSecureContainer
     */
    public function get3dsecure()
    {
        return $this->_3dsecure;
    }

    /**
     * @param \SprykerFeature\Zed\Payone\Business\Api\Request\Container\Invoicing\TransactionContainer $invoicing
     *
     * @return void
     */
    public function setInvoicing(TransactionContainer $invoicing)
    {
        $this->invoicing = $invoicing;
    }

    /**
     * @return \SprykerFeature\Zed\Payone\Business\Api\Request\Container\Invoicing\TransactionContainer
     */
    public function getInvoicing()
    {
        return $this->invoicing;
    }

    /**
     * @return string
     */
    public function getOnlinebanktransfertype()
    {
        return $this->onlinebanktransfertype;
    }

    /**
     * @param string $onlinebanktransfertype
     *
     * @return void
     */
    public function setOnlinebanktransfertype($onlinebanktransfertype)
    {
        $this->onlinebanktransfertype = $onlinebanktransfertype;
    }

}
