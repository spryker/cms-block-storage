<?php

/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace SprykerFeature\Zed\Payolution\Business\Payment\Handler\Transaction;

use Generated\Shared\Payolution\PayolutionResponseInterface;
use Generated\Shared\Payolution\CheckoutRequestInterface;

interface TransactionInterface
{
    /**
     * @param CheckoutRequestInterface $checkoutRequestTransfer
     *
     * @return PayolutionResponseInterface
     */
    public function preCheckPayment(CheckoutRequestInterface $checkoutRequestTransfer);

    /**
     * @param int $idPayment
     *
     * @return PayolutionResponseInterface
     */
    public function preAuthorizePayment($idPayment);

    /**
     * @param int $idPayment
     *
     * @return PayolutionResponseInterface
     */
    public function reAuthorizePayment($idPayment);

    /**
     * @param int $idPayment
     *
     * @return PayolutionResponseInterface
     */
    public function revertPayment($idPayment);

    /**
     * @param int $idPayment
     *
     * @return PayolutionResponseInterface
     */
    public function capturePayment($idPayment);

    /**
     * @param int $idPayment
     *
     * @return PayolutionResponseInterface
     */
    public function refundPayment($idPayment);

}
