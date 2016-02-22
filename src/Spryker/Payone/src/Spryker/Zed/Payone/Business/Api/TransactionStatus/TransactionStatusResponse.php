<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Payone\Business\Api\TransactionStatus;

class TransactionStatusResponse
{

    /**
     * part of payone specification
     */
    const STATUS_OK = 'TSOK';
    /**
     * not in payone specification, for the purpose if payone should queue transaction status and resend again
     */
    const STATUS_ERROR = 'TSERROR';

    /**
     * @var string
     */
    protected $status;

    /**
     * @var string
     */
    protected $errorMessage = '';

    /**
     * @param bool $isSuccess
     */
    public function __construct($isSuccess)
    {
        assert(is_bool($isSuccess));
        $this->status = $isSuccess ? self::STATUS_OK : self::STATUS_ERROR;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $output = $this->getStatus();
        if ($this->isError() && $this->getErrorMessage()) {
            $output .= ' : ' . $this->getErrorMessage();
        }

        return $output;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param string $errorMessage
     *
     * @return void
     */
    public function setErrorMessage($errorMessage)
    {
        $this->errorMessage = $errorMessage;
    }

    /**
     * @return string
     */
    public function getErrorMessage()
    {
        return $this->errorMessage;
    }

    /**
     * @return bool
     */
    public function isSuccess()
    {
        return ($this->getStatus() === self::STATUS_OK);
    }

    /**
     * @return bool
     */
    public function isError()
    {
        return (!$this->isSuccess());
    }

}
