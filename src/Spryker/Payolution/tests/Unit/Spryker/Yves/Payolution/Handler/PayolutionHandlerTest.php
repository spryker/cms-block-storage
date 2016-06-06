<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Unit\Spryker\Yves\Payolution\Dependency\Injector;

use Generated\Shared\Transfer\AddressTransfer;
use Generated\Shared\Transfer\CustomerTransfer;
use Generated\Shared\Transfer\PaymentTransfer;
use Generated\Shared\Transfer\PayolutionPaymentTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Spryker\Client\Payolution\PayolutionClientInterface;
use Spryker\Shared\Library\Currency\CurrencyManager;
use Spryker\Yves\Payolution\Exception\PaymentMethodNotFoundException;
use Spryker\Yves\Payolution\Handler\PayolutionHandler;
use Symfony\Component\HttpFoundation\Request;

/**
 * @group Spryker
 * @group Yves
 * @group Payolution
 * @group PayolutionHandler
 */
class PayolutionHandlerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @return void
     */
    public function testAddPaymentToQuoteShouldReturnQuoteTransfer()
    {
        $paymentHandler = new PayolutionHandler($this->getPayolutionClientMock(), CurrencyManager::getInstance());

        $request = Request::createFromGlobals();
        $quoteTransfer = new QuoteTransfer();

        $billingAddress = new AddressTransfer();
        $billingAddress->setSalutation('Mr');
        $billingAddress->setIso2Code('iso2Code');
        $quoteTransfer->setBillingAddress($billingAddress);

        $customerTransfer = new CustomerTransfer();
        $customerTransfer->setEmail('test@spryker.com');
        $quoteTransfer->setCustomer($customerTransfer);

        $paymentTransfer = new PaymentTransfer();
        $paymentTransfer->setPaymentSelection('payolutionInvoice');
        $payolutionPaymentTransfer = new PayolutionPaymentTransfer();
        $paymentTransfer->setPayolutionInvoice($payolutionPaymentTransfer);
        $quoteTransfer->setPayment($paymentTransfer);

        $result = $paymentHandler->addPaymentToQuote($request, $quoteTransfer);
        $this->assertInstanceOf(QuoteTransfer::class, $result);
    }

    /**
     * @return void
     */
    public function testGetPayolutionPaymentTransferShouldThrowExceptionIfPaymentSelectionNotFound()
    {
        $paymentHandler = new PayolutionHandler($this->getPayolutionClientMock(), CurrencyManager::getInstance());

        $request = Request::createFromGlobals();
        $quoteTransfer = new QuoteTransfer();
        $paymentTransfer = new PaymentTransfer();
        $paymentTransfer->setPaymentSelection('payolutionInvoice');
        $quoteTransfer->setPayment($paymentTransfer);

        $this->setExpectedException(PaymentMethodNotFoundException::class);

        $paymentHandler->addPaymentToQuote($request, $quoteTransfer);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\Spryker\Client\Payolution\PayolutionClientInterface
     */
    private function getPayolutionClientMock()
    {
        return $this->getMock(PayolutionClientInterface::class);
    }

}
