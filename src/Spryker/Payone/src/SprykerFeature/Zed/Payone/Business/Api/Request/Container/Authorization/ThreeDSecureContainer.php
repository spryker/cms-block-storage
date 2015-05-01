<?php

namespace SprykerFeature\Zed\Payone\Business\Api\Request\Container\Authorization;
use SprykerFeature\Zed\Payone\Business\Api\Request\Container\AbstractContainer;


class ThreeDSecureContainer extends AbstractContainer
{

    /**
     * @var string
     */
    protected $xid;
    /**
     * @var string
     */
    protected $cavv;
    /**
     * @var string
     */
    protected $eci;


    /**
     * @param string $cavv
     */
    public function setCavv($cavv)
    {
        $this->cavv = $cavv;
    }

    /**
     * @return string
     */
    public function getCavv()
    {
        return $this->cavv;
    }

    /**
     * @param string $eci
     */
    public function setEci($eci)
    {
        $this->eci = $eci;
    }

    /**
     * @return string
     */
    public function getEci()
    {
        return $this->eci;
    }

    /**
     * @param string $xid
     */
    public function setXid($xid)
    {
        $this->xid = $xid;
    }

    /**
     * @return string
     */
    public function getXid()
    {
        return $this->xid;
    }

}
