<?php
/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Unit\Spryker\Zed\Ratepay\Business\Api\Converter;

class InstallmentPaymentMapperTest extends AbstractMapperTest
{

    public function testMapper()
    {
        $installment = $this->mockRatepayPaymentInstallmentTransfer();
        $quote = $this->mockQuoteTransfer();
        $quote->getPayment()
            ->setRatepayInstallment($installment);

        $this->mapperFactory
            ->getBasketMapper(
                $quote,
                $installment
            )
            ->map();

        $this->mapperFactory
            ->getInstallmentPaymentMapper(
                $quote,
                $installment
            )
            ->map();

        $this->assertEquals('invoice', $this->requestTransfer->getInstallmentPayment()->getDebitPayType());
        $this->assertEquals('125.7', $this->requestTransfer->getInstallmentPayment()->getAmount());
    }

}
