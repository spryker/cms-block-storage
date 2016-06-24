<?php
/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Functional\Spryker\Zed\Ratepay\Business\Request\Payment\Cancel;

use Functional\Spryker\Zed\Ratepay\Business\Api\Adapter\Http\CancelAdapterMock;
use Functional\Spryker\Zed\Ratepay\Business\Request\Payment\InvoiceAbstractTest;

class InvoiceTest extends InvoiceAbstractTest
{

    /**
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->setUpSalesOrderTestData();
        $this->setUpPaymentTestData();

        $this->orderTransfer->fromArray($this->orderEntity->toArray(), true);
    }

    /**
     * @return \Functional\Spryker\Zed\Ratepay\Business\Api\Adapter\Http\CancelAdapterMock
     */
    protected function getPaymentSuccessResponseAdapterMock()
    {
        return new CancelAdapterMock();
    }

    /**
     * @return \Functional\Spryker\Zed\Ratepay\Business\Api\Adapter\Http\CancelAdapterMock
     */
    protected function getPaymentFailureResponseAdapterMock()
    {
        return (new CancelAdapterMock())->expectFailure();
    }

    /**
     * @param \Spryker\Zed\Ratepay\Business\RatepayFacade $facade
     *
     * @return \Generated\Shared\Transfer\RatepayResponseTransfer
     */
    protected function runFacadeMethod($facade)
    {
        return $facade->cancelPayment($this->orderTransfer, $this->orderTransfer->getItems()->getArrayCopy());
    }

}
