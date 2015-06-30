<?php
/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace SprykerFeature\Zed\PayoneCheckoutConnector\Communication\Plugin;

use SprykerFeature\Zed\Checkout\Dependency\Plugin\CheckoutOrderHydrationInterface;
use SprykerEngine\Zed\Kernel\Communication\AbstractPlugin;
use SprykerFeature\Zed\Payone\Business\PayoneDependencyContainer;
use Generated\Shared\Transfer\OrderTransfer;
use Generated\Shared\Transfer\CheckoutRequestTransfer;
/**
 * @method PayoneDependencyContainer getDependencyContainer()
 */
class CheckoutOrderHydrationPlugin extends AbstractPlugin implements CheckoutOrderHydrationInterface{

    /**
     * @param OrderTransfer $orderTransfer
     * @param CheckoutRequestTransfer $checkoutRequest
     */
    public function hydrateOrder(OrderTransfer $orderTransfer, CheckoutRequestTransfer $checkoutRequest)
    {
        $orderTransfer->setPayonePayment($checkoutRequest->getPayonePayment());
    }

}
