<?php
/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace Functional\SprykerFeature\Zed\Payolution\Business;

use Functional\SprykerFeature\Zed\Payolution\Business\Api\Adapter\Http\PreCheckAdapterMock;
use Generated\Shared\Transfer\OrderTransfer;
use Generated\Shared\Transfer\PayolutionPaymentTransfer;
use Generated\Shared\Transfer\TotalsTransfer;
use SprykerFeature\Zed\Payolution\Business\Api\Constants;

class PayolutionFacadePreCheckTest extends AbstractFacadeTest
{

    public function testPreCheckPaymentWithSuccessResponse()
    {
        $adapterMock = new PreCheckAdapterMock();
        $facade = $this->getFacadeMock($adapterMock);
        $response = $facade->preCheckPayment($this->getOrderTransfer());

        $this->assertInstanceOf('Generated\Shared\Transfer\PayolutionResponseTransfer', $response);

        $expectedResponseData = $adapterMock->getSuccessResponse();
        $expectedResponse = $this->getResponseConverter()->fromArray($expectedResponseData);

        $this->assertEquals($expectedResponse, $response);
        $this->assertSame($expectedResponse->getProcessingReasonCode(), $response->getProcessingReasonCode());
        $this->assertSame($expectedResponse->getProcessingStatusCode(), $response->getProcessingStatusCode());
    }

    public function testPreCheckPaymentWithFailureResponse()
    {
        $adapterMock = (new PreCheckAdapterMock())->expectFailure();
        $facade = $this->getFacadeMock($adapterMock);
        $response = $facade->preCheckPayment($this->getOrderTransfer());

        $this->assertInstanceOf('Generated\Shared\Transfer\PayolutionResponseTransfer', $response);

        $expectedResponseData = $adapterMock->getFailureResponse();
        $expectedResponse = $this->getResponseConverter()->fromArray($expectedResponseData);

        $this->assertEquals($expectedResponse, $response);
        $this->assertSame($expectedResponse->getProcessingReasonCode(), $response->getProcessingReasonCode());
        $this->assertSame($expectedResponse->getProcessingStatusCode(), $response->getProcessingStatusCode());
    }

    /**
     * @return OrderTransfer
     */
    private function getOrderTransfer()
    {
        $totalsTransfer = (new TotalsTransfer())->setGrandTotal(100000);

        $paymentTransfer = (new PayolutionPaymentTransfer())
            ->setFirstName('John')
            ->setLastName('Doe')
            ->setGender('Male')
            ->setSalutation('Mr')
            ->setDateOfBirth('1970-01-01')
            ->setCountryIso2Code('de')
            ->setCity('Berlin')
            ->setStreet('Straße des 17. Juni 135')
            ->setZipCode('10623')
            ->setClientIp('127.0.0.1')
            ->setEmail('john@doe.com')
            ->setAccountBrand(Constants::ACCOUNT_BRAND_INVOICE);

        $orderTransfer = (new OrderTransfer())
            ->setIdSalesOrder(1)
            ->setPayolutionPayment($paymentTransfer)
            ->setTotals($totalsTransfer);

        return $orderTransfer;
    }

}
