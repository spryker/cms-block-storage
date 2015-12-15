<?php

/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace Spryker\Zed\Payone\Business\Api\Request\Container;

use Spryker\Shared\Payone\PayoneApiConstants;

class ThreeDSecureCheckContainer extends AbstractRequestContainer
{

    /**
     * @var string
     */
    protected $request = PayoneApiConstants::REQUEST_TYPE_3DSECURE_CHECK;

    /**
     * @var int
     */
    protected $aid;

    /**
     * @var int
     */
    protected $amount;

    /**
     * @var string
     */
    protected $currency;

    /**
     * @var string
     */
    protected $clearingtype;

    /**
     * @var string
     */
    protected $exiturl;

    /**
     * @var string
     */
    protected $cardpan;

    /**
     * @var string
     */
    protected $cardtype;

    /**
     * @var string
     */
    protected $cardexpiredate;

    /**
     * @var int
     */
    protected $cardcvc2;

    /**
     * @var string
     */
    protected $storecarddata;

    /**
     * @var string
     */
    protected $language;

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
     * @param int $cardcvc2
     *
     * @return void
     */
    public function setCardCvc2($cardcvc2)
    {
        $this->cardcvc2 = $cardcvc2;
    }

    /**
     * @return int
     */
    public function getCardCvc2()
    {
        return $this->cardcvc2;
    }

    /**
     * @param string $cardexpiredate
     *
     * @return void
     */
    public function setCardExpireDate($cardexpiredate)
    {
        $this->cardexpiredate = $cardexpiredate;
    }

    /**
     * @return string
     */
    public function getCardExpireDate()
    {
        return $this->cardexpiredate;
    }

    /**
     * @param string $cardpan
     *
     * @return void
     */
    public function setCardPan($cardpan)
    {
        $this->cardpan = $cardpan;
    }

    /**
     * @return string
     */
    public function getCardPan()
    {
        return $this->cardpan;
    }

    /**
     * @param string $cardtype
     *
     * @return void
     */
    public function setCardType($cardtype)
    {
        $this->cardtype = $cardtype;
    }

    /**
     * @return string
     */
    public function getCardType()
    {
        return $this->cardtype;
    }

    /**
     * @param string $clearingtype
     *
     * @return void
     */
    public function setClearingType($clearingtype)
    {
        $this->clearingtype = $clearingtype;
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
     * @param string $exiturl
     *
     * @return void
     */
    public function setExitUrl($exiturl)
    {
        $this->exiturl = $exiturl;
    }

    /**
     * @return string
     */
    public function getExitUrl()
    {
        return $this->exiturl;
    }

    /**
     * @param string $language
     *
     * @return void
     */
    public function setLanguage($language)
    {
        $this->language = $language;
    }

    /**
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @param string $storecarddata
     *
     * @return void
     */
    public function setStoreCardData($storecarddata)
    {
        $this->storecarddata = $storecarddata;
    }

    /**
     * @return string
     */
    public function getStoreCardData()
    {
        return $this->storecarddata;
    }

}
