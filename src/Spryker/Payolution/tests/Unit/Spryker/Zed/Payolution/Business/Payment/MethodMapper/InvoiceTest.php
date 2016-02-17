<?php

/**
 * (c) Spryker Systems GmbH copyright protected
 */
namespace Unit\Spryker\Zed\Payolution\Business\Payment\MethodMapper;

use Codeception\TestCase\Test;
use Generated\Shared\Transfer\AddressTransfer;
use Generated\Shared\Transfer\OrderTransfer;
use Generated\Shared\Transfer\PaymentTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\PayolutionPaymentTransfer;
use Generated\Shared\Transfer\TotalsTransfer;
use Spryker\Zed\Payolution\Business\Payment\Method\ApiConstants;
use Spryker\Zed\Payolution\Business\Payment\Method\Invoice\Invoice;
use Spryker\Zed\Payolution\PayolutionConfig;
use Orm\Zed\Payolution\Persistence\Map\SpyPaymentPayolutionTableMap;

class InvoiceTest extends Test
{

    /**
     * @return void
     */
    public function testMapToPreCheck()
    {
        $quoteTransfer = $this->getQuoteTransfer();
        $methodMapper = new Invoice($this->getBundleConfigMock());
        $requestData = $methodMapper->buildPreCheckRequest($quoteTransfer);

        $this->assertSame(ApiConstants::BRAND_INVOICE, $requestData['ACCOUNT.BRAND']);
        $this->assertSame(ApiConstants::PAYMENT_CODE_PRE_CHECK, $requestData['PAYMENT.CODE']);
        $this->assertSame('Straße des 17. Juni 135', $requestData['ADDRESS.STREET']);
        $this->assertSame(ApiConstants::CRITERION_PRE_CHECK, 'CRITERION.PAYOLUTION_PRE_CHECK');
        $this->assertSame('TRUE', $requestData['CRITERION.PAYOLUTION_PRE_CHECK']);
    }

    /**
     * @return QuoteTransfer
     */
    private function getQuoteTransfer()
    {
        $quoteTransfer = new QuoteTransfer();

        $totalsTransfer = new TotalsTransfer();
        $totalsTransfer
            ->setGrandTotal(10000)
            ->setSubtotal(10000);

        $quoteTransfer->setTotals($totalsTransfer);

        $addressTransfer = new AddressTransfer();
        $addressTransfer
            ->setFirstName('John')
            ->setLastName('Doe')
            ->setSalutation('Mr')
            ->setCity('Berlin')
            ->setIso2Code('de')
            ->setAddress1('Straße des 17. Juni')
            ->setAddress2('135')
            ->setZipCode('10623');

        $quoteTransfer->setBillingAddress($addressTransfer);

        $paymentTransfer = new PayolutionPaymentTransfer();
        $paymentTransfer
            ->setGender('Male')
            ->setDateOfBirth('1970-01-01')
            ->setClientIp('127.0.0.1')
            ->setAccountBrand(ApiConstants::BRAND_INVOICE)
            ->setAddress($addressTransfer);

        $payment = new PaymentTransfer();
        $payment->setPayolution($paymentTransfer);
        $quoteTransfer->setPayment($payment);

        return $quoteTransfer;
    }

    /**
     * @return void
     */
    public function testMapToPreAuthorization()
    {
        $methodMapper = new Invoice($this->getBundleConfigMock());
        $paymentEntityMock = $this->getPaymentEntityMock();
        $orderTransfer = $this->createOrderTransfer();
        $requestData = $methodMapper->buildPreAuthorizationRequest($orderTransfer, $paymentEntityMock);

        $this->assertSame($paymentEntityMock->getEmail(), $requestData['CONTACT.EMAIL']);
        $this->assertSame(ApiConstants::BRAND_INVOICE, $requestData['ACCOUNT.BRAND']);
        $this->assertSame(ApiConstants::PAYMENT_CODE_PRE_AUTHORIZATION, $requestData['PAYMENT.CODE']);
    }

    /**
     * @return void
     */
    public function testMapToReAuthorization()
    {
        $uniqueId = uniqid('test_');
        $methodMapper = new Invoice($this->getBundleConfigMock());
        $paymentEntityMock = $this->getPaymentEntityMock();
        $orderTransfer = $this->createOrderTransfer();
        $requestData = $methodMapper->buildReAuthorizationRequest($orderTransfer, $paymentEntityMock, $uniqueId);

        $this->assertSame(ApiConstants::BRAND_INVOICE, $requestData['ACCOUNT.BRAND']);
        $this->assertSame(ApiConstants::PAYMENT_CODE_RE_AUTHORIZATION, $requestData['PAYMENT.CODE']);
        $this->assertSame($uniqueId, $requestData['IDENTIFICATION.REFERENCEID']);
    }

    /**
     * @return void
     */
    public function testMapToReversal()
    {
        $uniqueId = uniqid('test_');
        $methodMapper = new Invoice($this->getBundleConfigMock());
        $paymentEntityMock = $this->getPaymentEntityMock();
        $orderTransfer = $this->createOrderTransfer();
        $requestData = $methodMapper->buildRevertRequest($orderTransfer, $paymentEntityMock, $uniqueId);

        $this->assertSame(ApiConstants::BRAND_INVOICE, $requestData['ACCOUNT.BRAND']);
        $this->assertSame(ApiConstants::PAYMENT_CODE_REVERSAL, $requestData['PAYMENT.CODE']);
        $this->assertSame($uniqueId, $requestData['IDENTIFICATION.REFERENCEID']);
    }

    /**
     * @return void
     */
    public function testMapToCapture()
    {
        $uniqueId = uniqid('test_');
        $methodMapper = new Invoice($this->getBundleConfigMock());
        $paymentEntityMock = $this->getPaymentEntityMock();
        $orderTransfer = $this->createOrderTransfer();
        $requestData = $methodMapper->buildCaptureRequest($orderTransfer, $paymentEntityMock, $uniqueId);

        $this->assertSame(ApiConstants::BRAND_INVOICE, $requestData['ACCOUNT.BRAND']);
        $this->assertSame(ApiConstants::PAYMENT_CODE_CAPTURE, $requestData['PAYMENT.CODE']);
        $this->assertSame($uniqueId, $requestData['IDENTIFICATION.REFERENCEID']);
    }

    /**
     * @return void
     */
    public function testMapToRefund()
    {
        $uniqueId = uniqid('test_');
        $methodMapper = new Invoice($this->getBundleConfigMock());
        $paymentEntityMock = $this->getPaymentEntityMock();
        $orderTransfer = $this->createOrderTransfer();
        $requestData = $methodMapper->buildRefundRequest($orderTransfer, $paymentEntityMock, $uniqueId);

        $this->assertSame(ApiConstants::BRAND_INVOICE, $requestData['ACCOUNT.BRAND']);
        $this->assertSame(ApiConstants::PAYMENT_CODE_REFUND, $requestData['PAYMENT.CODE']);
        $this->assertSame($uniqueId, $requestData['IDENTIFICATION.REFERENCEID']);
    }

    /**
     * @return OrderTransfer
     */
    protected function createOrderTransfer()
    {
        $orderTransfer = new OrderTransfer();
        $totalTransfer = new TotalsTransfer();
        $totalTransfer->setGrandTotal(1000);
        $orderTransfer->setTotals($totalTransfer);

        return $orderTransfer;
    }

    /**
     * @return PayolutionConfig
     */
    private function getBundleConfigMock()
    {
        return $this->getMock(
            'Spryker\Zed\Payolution\PayolutionConfig',
            [],
            [],
            '',
            false
        );
    }

    /**
     * @return SpyPaymentPayolution
     */
    private function getPaymentEntityMock()
    {
        $orderEntityMock = $this->getMock(
            'Orm\Zed\Sales\Persistence\SpySalesOrder',
            []
        );

        /** @var SpyPaymentPayolution|\PHPUnit_Framework_MockObject_MockObject $paymentEntityMock */
        $paymentEntityMock = $this->getMock(
            'Orm\Zed\Payolution\Persistence\SpyPaymentPayolution',
            [
                'getSpySalesOrder',
            ]
        );
        $paymentEntityMock
            ->expects($this->any())
            ->method('getSpySalesOrder')
            ->will($this->returnValue($orderEntityMock));

        $paymentEntityMock
            ->setIdPaymentPayolution(1)
            ->setClientIp('127.0.0.1')
            ->setAccountBrand(ApiConstants::BRAND_INVOICE)
            ->setFirstName('John')
            ->setLastName('Doe')
            ->setEmail('john@doe.com')
            ->setSalutation('Mr')
            ->setDateOfBirth('1970-01-01')
            ->setCountryIso2Code('de')
            ->setCity('Berlin')
            ->setStreet('Straße des 17. Juni 135')
            ->setZipCode('10623')
            ->setGender(SpyPaymentPayolutionTableMap::COL_GENDER_FEMALE);

        return $paymentEntityMock;
    }

}
