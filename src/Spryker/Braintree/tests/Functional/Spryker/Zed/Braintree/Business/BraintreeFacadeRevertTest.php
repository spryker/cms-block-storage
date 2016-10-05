<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Functional\Spryker\Zed\Braintree\Business;

use Braintree\Result\Successful;
use Braintree\Transaction;
use Braintree\Transaction\StatusDetails;
use DateTime;
use Spryker\Zed\Braintree\BraintreeConfig;
use Spryker\Zed\Braintree\Business\BraintreeFacade;
use Spryker\Zed\Braintree\Business\Payment\Transaction\RevertTransaction;

/**
 * @group Functional
 * @group Spryker
 * @group Zed
 * @group Braintree
 * @group Business
 * @group BraintreeFacadeRevertTest
 */
class BraintreeFacadeRevertTest extends AbstractFacadeTest
{

    /**
     * @return void
     */
    public function testRevertPaymentWithSuccessResponse()
    {
        $braintreeFacade = new BraintreeFacade();
        $factoryMock = $this->getFactoryMock(['createRevertTransaction']);
        $factoryMock->expects($this->once())->method('createRevertTransaction')->willReturn(
            $this->getRevertTransactionMock()
        );
        $braintreeFacade->setFactory($factoryMock);

        $transactionMetaTransfer = $this->getTransactionMetaTransfer();

        $response = $braintreeFacade->revertPayment($transactionMetaTransfer);

        $this->assertTrue($response->getIsSuccess());
    }

    /**
     * @return void
     */
    public function testRevertPaymentWithErrorResponse()
    {
        $factoryMock = $this->getFactoryMock(['createRevertTransaction']);
        $factoryMock->expects($this->once())->method('createRevertTransaction')->willReturn(
            $this->getRevertTransactionMock(false)
        );
        $braintreeFacade = $this->getBraintreeFacade($factoryMock);

        $transactionMetaTransfer = $this->getTransactionMetaTransfer();
        $response = $braintreeFacade->revertPayment($transactionMetaTransfer);

        $this->assertFalse($response->getIsSuccess());
    }

    /**
     * @param bool $success
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|\Spryker\Zed\Braintree\Business\Payment\Transaction\RevertTransaction
     */
    protected function getRevertTransactionMock($success = true)
    {
        $revertTransactionMock = $this
            ->getMockBuilder(RevertTransaction::class)
            ->setMethods(['revert'])
            ->setConstructorArgs(
                [new BraintreeConfig()]
            )
            ->getMock();

        if ($success) {
            $revertTransactionMock->method('revert')->willReturn($this->getSuccessResponse());
        } else {
            $revertTransactionMock->method('revert')->willReturn($this->getErrorResponse());
        }

        return $revertTransactionMock;
    }

    /**
     * @return \Braintree\Result\Successful
     */
    protected function getSuccessResponse()
    {
        $transaction = Transaction::factory([
            'id' => 123,
            'processorResponseCode' => '1000',
            'processorResponseText' => 'Approved',
            'createdAt' => new DateTime(),
            'status' => 'revert',
            'type' => 'sale',
            'amount' => $this->createOrderTransfer()->getTotals()->getGrandTotal() / 100,
            'merchantAccountId' => 'abc',
            'statusHistory' => new StatusDetails([
                'timestamp' => new DateTime(),
                'status' => 'voided'
            ])
        ]);
        $response = new Successful([$transaction]);

        return $response;
    }

}
