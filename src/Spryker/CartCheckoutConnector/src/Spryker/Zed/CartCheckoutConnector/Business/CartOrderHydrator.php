<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\CartCheckoutConnector\Business;

use Generated\Shared\Transfer\CheckoutRequestTransfer;
use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\OrderTransfer;

class CartOrderHydrator implements CartOrderHydratorInterface
{

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $order
     * @param \Generated\Shared\Transfer\CheckoutRequestTransfer $request
     *
     * @return void
     */
    public function hydrateOrderTransfer(OrderTransfer $order, CheckoutRequestTransfer $request)
    {
        $cart = $request->getCart();

        $order->setItems($this->transformCartItemsToOrderItems($cart->getItems()))
            ->setTotals($cart->getTotals())
            ->setExpenses($cart->getExpenses());
    }

    /**
     * @param \ArrayObject|\Generated\Shared\Transfer\ItemTransfer[] $cartItems
     *
     * @return \ArrayObject
     */
    protected function transformCartItemsToOrderItems(\ArrayObject $cartItems)
    {
        $orderItems = [];
        foreach ($cartItems as $cartItem) {
            if ($cartItem->getQuantity() > 1) {
                $orderItems = array_merge($orderItems, $this->expandCartItem($cartItem));
            } else {
                $orderItems[] = $this->createItemTransfer($cartItem);
            }
        }

        return new \ArrayObject($orderItems);
    }

    /**
     * @param \Generated\Shared\Transfer\ItemTransfer $cartItem
     *
     * @return array
     */
    protected function expandCartItem(ItemTransfer $cartItem)
    {
        $result = [];
        $quantity = $cartItem->getQuantity();
        for ($i = 1; $i <= $quantity; ++$i) {
            $result[] = $this->createItemTransfer($cartItem);
        }

        return $result;
    }

    /**
     * @return \Generated\Shared\Transfer\ItemTransfer
     */
    protected function getItemTransfer()
    {
        return new ItemTransfer();
    }

    /**
     * @param \Generated\Shared\Transfer\ItemTransfer $cartItemTransfer
     *
     * @return \Generated\Shared\Transfer\ItemTransfer
     */
    protected function createItemTransfer(ItemTransfer $cartItemTransfer)
    {
        $orderItemTransfer = $this->getItemTransfer();
        $orderItemTransfer->fromArray($cartItemTransfer->toArray(), true);
        $orderItemTransfer->setGroupKey(null);
        $orderItemTransfer->setQuantity(1);

        return $orderItemTransfer;
    }

}
