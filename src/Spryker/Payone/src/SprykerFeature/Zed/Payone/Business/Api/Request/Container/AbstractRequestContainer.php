<?php
/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace SprykerFeature\Zed\Payone\Business\Api\Request\Container;
use SprykerFeature\Zed\Payone\Business\Api\Request\Container\AbstractContainer;
use SprykerFeature\Shared\Payone\PayoneApiConstants;


abstract class AbstractRequestContainer extends AbstractContainer implements PayoneApiConstants
{

    /**
     * @var int
     */
    protected $mid;

    /**
     * @var int
     */
    protected $portalid;

    /**
     * @var string
     */
    protected $key;

    /**
     * @var string
     */
    protected $mode;

    /**
     * @var string
     */
    protected $request;

    /**
     * @var string
     */
    protected $encoding;

    /**
     * name of the solution-partner (company)
     * @var string
     */
    protected $solution_name;

    /**
     * version of the solution-partner's app / extension / plugin / etc..
     * @var string
     */
    protected $solution_version;

    /**
     * system-name
     * @var string
     */
    protected $integrator_name;

    /**
     * system-version
     * @var string
     */
    protected $integrator_version;


    /**
     * @param string $encoding
     */
    public function setEncoding($encoding)
    {
        $this->encoding = $encoding;
    }

    /**
     * @return string
     */
    public function getEncoding()
    {
        return $this->encoding;
    }

    /**
     * @param string $key
     */
    public function setKey($key)
    {
        $this->key = $key;
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @param int $mid
     */
    public function setMid($mid)
    {
        $this->mid = $mid;
    }

    /**
     * @return int
     */
    public function getMid()
    {
        return $this->mid;
    }

    /**
     * @param string $mode
     */
    public function setMode($mode)
    {
        $this->mode = $mode;
    }

    /**
     * @return string
     */
    public function getMode()
    {
        return $this->mode;
    }

    /**
     * @param int $portalid
     */
    public function setPortalid($portalid)
    {
        $this->portalid = $portalid;
    }

    /**
     * @return int
     */
    public function getPortalid()
    {
        return $this->portalid;
    }

    /**
     * @param string $request
     */
    public function setRequest($request)
    {
        $this->request = $request;
    }

    /**
     * @return string
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * set the system-Name
     * @param string $integrator_name
     */
    public function setIntegratorName($integrator_name)
    {
        $this->integrator_name = $integrator_name;
    }

    /**
     * @return string
     */
    public function getIntegratorName()
    {
        return $this->integrator_name;
    }

    /**
     * set the system-version
     * @param string $integrator_version
     */
    public function setIntegratorVersion($integrator_version)
    {
        $this->integrator_version = $integrator_version;
    }

    /**
     * @return string
     */
    public function getIntegratorVersion()
    {
        return $this->integrator_version;
    }

    /**
     * set the name of the solution-partner (company)
     * @param string $solution_name
     */
    public function setSolutionName($solution_name)
    {
        $this->solution_name = $solution_name;
    }

    /**
     * @return string
     */
    public function getSolutionName()
    {
        return $this->solution_name;
    }

    /**
     * set the version of the solution-partner's app / extension / plugin / etc..
     * @param string $solution_version
     */
    public function setSolutionVersion($solution_version)
    {
        $this->solution_version = $solution_version;
    }

    /**
     * @return string
     */
    public function getSolutionVersion()
    {
        return $this->solution_version;
    }

}
