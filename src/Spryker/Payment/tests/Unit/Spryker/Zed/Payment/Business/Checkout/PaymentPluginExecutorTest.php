<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Unit\Spryker\Zed\Payment\Business\Checkout;

use Generated\Shared\Transfer\CheckoutResponseTransfer;
use Generated\Shared\Transfer\PaymentTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Spryker\Zed\Payment\Business\Checkout\PaymentPluginExecutor;
use Spryker\Zed\Payment\Dependency\Plugin\Checkout\CheckoutPluginCollection;
use Spryker\Zed\Payment\Dependency\Plugin\Checkout\CheckoutPostCheckPluginInterface;
use Spryker\Zed\Payment\Dependency\Plugin\Checkout\CheckoutPreCheckPluginInterface;
use Spryker\Zed\Payment\Dependency\Plugin\Checkout\CheckoutSaveOrderPluginInterface;
use Spryker\Zed\Payment\PaymentDependencyProvider;

/**
 * @group Unit
 * @group Spryker
 * @group Zed
 * @group Payment
 * @group Business
 * @group Checkout
 * @group PaymentPluginExecutorTest
 */
class PaymentPluginExecutorTest extends \PHPUnit_Framework_TestCase
{

    const TEST_PROVIDER = 'Test';

    /**
     * @return void
     */
    public function testPreCheckShouldTriggerTestPaymentPlugin()
    {
        $preCheckPluginMock = $this->createPreCheckPluginMock();
        $preCheckPluginMock->expects($this->once())->method('execute');

        $paymentPluginExecutor = $this->createPaymentPluginExecutor($preCheckPluginMock);
        $quoteTransfer = $this->createQuoteTransfer();
        $checkoutResponseTransfer = new CheckoutResponseTransfer();

        $paymentPluginExecutor->executePreCheckPlugin($quoteTransfer, $checkoutResponseTransfer);
    }

    /**
     * @return void
     */
    public function testOrderSaverShouldTriggerTestPaymentPlugin()
    {
        $orderSavePluginMock = $this->createSavePluginMock();
        $orderSavePluginMock->expects($this->once())->method('execute');

        $paymentPluginExecutor = $this->createPaymentPluginExecutor(null, $orderSavePluginMock);
        $quoteTransfer = $this->createQuoteTransfer();
        $checkoutResponseTransfer = new CheckoutResponseTransfer();

        $paymentPluginExecutor->executeOrderSaverPlugin($quoteTransfer, $checkoutResponseTransfer);
    }

    /**
     * @return void
     */
    public function testPostCheckShouldTriggerTestPaymentPlugin()
    {
        $postCheckoutPluginMock = $this->createPostSavePluginMock();
        $postCheckoutPluginMock->expects($this->once())->method('execute');

        $paymentPluginExecutor = $this->createPaymentPluginExecutor(null, null, $postCheckoutPluginMock);
        $quoteTransfer = $this->createQuoteTransfer();
        $checkoutResponseTransfer = new CheckoutResponseTransfer();

        $paymentPluginExecutor->executePostCheckPlugin($quoteTransfer, $checkoutResponseTransfer);
    }

    /**
     * @param null $preCheckPluginMock
     * @param null $orderSavePluginMock
     * @param null $postCheckPluginMock
     *
     * @return \Spryker\Zed\Payment\Business\Checkout\PaymentPluginExecutor
     */
    protected function createPaymentPluginExecutor(
        $preCheckPluginMock = null,
        $orderSavePluginMock = null,
        $postCheckPluginMock = null
    ) {
        $paymentPluginExecutor = new PaymentPluginExecutor(
            $this->createCheckoutPlugins(
                $preCheckPluginMock,
                $orderSavePluginMock,
                $postCheckPluginMock
            )
        );

        return $paymentPluginExecutor;
    }

    /**
     * @param null $preCheckPluginMock
     * @param null $orderSavePluginMock
     * @param null $postCheckPluginMock
     *
     * @return \Spryker\Zed\Payment\Dependency\Plugin\Checkout\CheckoutPluginCollectionInterface
     */
    protected function createCheckoutPlugins(
        $preCheckPluginMock = null,
        $orderSavePluginMock = null,
        $postCheckPluginMock = null
    ) {
        $pluginCollection = new CheckoutPluginCollection();
        if ($preCheckPluginMock !== null) {
            $pluginCollection->add($preCheckPluginMock, self::TEST_PROVIDER, PaymentDependencyProvider::CHECKOUT_PRE_CHECK_PLUGINS);
        }
        if ($orderSavePluginMock !== null) {
            $pluginCollection->add($orderSavePluginMock, self::TEST_PROVIDER, PaymentDependencyProvider::CHECKOUT_ORDER_SAVER_PLUGINS);
        }
        if ($postCheckPluginMock !== null) {
            $pluginCollection->add($postCheckPluginMock, self::TEST_PROVIDER, PaymentDependencyProvider::CHECKOUT_POST_SAVE_PLUGINS);
        }

        return $pluginCollection;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\Spryker\Zed\Payment\Dependency\Plugin\Checkout\CheckoutPreCheckPluginInterface
     */
    protected function createPreCheckPluginMock()
    {
        return $this->getMockBuilder(CheckoutPreCheckPluginInterface::class)->getMock();
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\Spryker\Zed\Payment\Dependency\Plugin\Checkout\CheckoutSaveOrderPluginInterface
     */
    protected function createSavePluginMock()
    {
        return $this->getMockBuilder(CheckoutSaveOrderPluginInterface::class)->getMock();
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\Spryker\Zed\Payment\Dependency\Plugin\Checkout\CheckoutPostCheckPluginInterface
     */
    protected function createPostSavePluginMock()
    {
        return $this->getMockBuilder(CheckoutPostCheckPluginInterface::class)->getMock();
    }

    /**
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    protected function createQuoteTransfer()
    {
        $quoteTransfer = new QuoteTransfer();
        $paymentTransfer = new PaymentTransfer();
        $paymentTransfer->setPaymentProvider(self::TEST_PROVIDER);
        $quoteTransfer->setPayment($paymentTransfer);

        return $quoteTransfer;
    }

}
