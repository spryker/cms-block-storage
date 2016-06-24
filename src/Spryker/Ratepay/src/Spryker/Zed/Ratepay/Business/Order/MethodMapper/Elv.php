<?php
/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Ratepay\Business\Order\MethodMapper;

use Generated\Shared\Transfer\QuoteTransfer;
use Orm\Zed\Ratepay\Persistence\SpyPaymentRatepay;
use Spryker\Shared\Ratepay\RatepayConstants;

class Elv extends AbstractMapper
{

    /**
     * @const string Method name.
     */
    const METHOD = RatepayConstants::METHOD_ELV;

    /**
     * @return string
     */
    public function getMethodName()
    {
        return static::METHOD;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\RatepayPaymentElvTransfer
     */
    protected function getPaymentTransfer(QuoteTransfer $quoteTransfer)
    {
        return $quoteTransfer->getPayment()->getRatepayElv();
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Orm\Zed\Ratepay\Persistence\SpyPaymentRatepay $payment
     *
     * @return void
     */
    public function mapMethodDataToPayment(QuoteTransfer $quoteTransfer, SpyPaymentRatepay $payment)
    {
        parent::mapMethodDataToPayment($quoteTransfer, $payment);

        $paymentTransfer = $this->getPaymentTransfer($quoteTransfer);
        $payment->setBankAccountBic($paymentTransfer->requireBankAccountBic()->getBankAccountBic())
            ->setBankAccountHolder($paymentTransfer->requireBankAccountHolder()->getBankAccountHolder())
            ->setBankAccountIban($paymentTransfer->requireBankAccountIban()->getBankAccountIban());
    }

}
