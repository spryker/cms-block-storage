<?php
/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace SprykerFeature\Zed\Cart\Business\StorageProvider;

use Generated\Shared\Cart\ChangeInterface;
use SprykerFeature\Zed\Cart\Business\Exception\InvalidArgumentException;
use Generated\Shared\Cart\CartInterface;
use Generated\Shared\Cart\CartItemInterface;

class InMemoryProvider implements StorageProviderInterface
{
    /**
     * @param CartInterface $cart
     * @param ChangeInterface $change
     *
     * @return CartInterface
     */
    public function addItems(CartInterface $cart, ChangeInterface $change)
    {
        $existingItems = $cart->getItems();
        $skuIndex = $this->createSkuIndex($existingItems);

        foreach ($change->getItems() as $item) {
            if ($item->getQuantity() < 1) {
                throw new InvalidArgumentException(
                    sprintf('Could not increase cart item %d with %d as value', $item->getId(), $item->getQuantity())
                );
            }

            if (isset($skuIndex[$item->getId()])) {
                $existingItem = $existingItems->offsetGet($skuIndex[$item->getId()]);
                $existingItem->setQuantity($existingItem->getQuantity() + $item->getQuantity());
            } else {
                $existingItems->append($item);
            }
        }

        return $cart;
    }

    /**
     * @param CartInterface $cart
     * @param ChangeInterface $change
     *
     * @return CartInterface
     */
    public function removeItems(CartInterface $cart, ChangeInterface $change)
    {
        $existingItems = $cart->getItems();
        $skuIndex = $this->createSkuIndex($existingItems);

        foreach ($change->getItems() as $item) {
            if ($item->getQuantity() < 1) {
                throw new InvalidArgumentException(
                    sprintf('Could not decrease cart item %d with %d as value', $item->getId(), $item->getQuantity())
                );
            }

            if (isset($skuIndex[$item->getId()])) {
                $this->decreaseExistingItem($existingItems, $skuIndex[$item->getId()], $item);
            }
        }

        return $cart;
    }

    /**
     * @param CartInterface $cart
     * @param ChangeInterface $increasedItems
     *
     * @return CartInterface
     */
    public function increaseItems(CartInterface $cart, ChangeInterface $increasedItems)
    {
        return $this->addItems($cart, $increasedItems);
    }

    /**
     * @param CartInterface $cart
     * @param ChangeInterface $decreasedItems
     *
     * @return CartInterface
     */
    public function decreaseItems(CartInterface $cart, ChangeInterface $decreasedItems)
    {
        return $this->removeItems($cart, $decreasedItems);
    }

    /**
     * @param CartItemInterface[] $existingItems
     * @param int $index
     * @param CartItemInterface $item
     */
    private function decreaseExistingItem($existingItems, $index, $item)
    {
        $existingItem = $existingItems[$index];
        $newQuantity = $existingItem->getQuantity() - $item->getQuantity();

        if ($newQuantity > 0) {
            $existingItem->setQuantity($newQuantity);
        } else {
            unset($existingItems[$index]);
        }
    }

    /**
     * @param \ArrayObject|CartItemInterface[] $cartItems
     *
     * @return array
     */
    protected function createSkuIndex(\ArrayObject $cartItems)
    {
        $skuIndex = [];

        foreach ($cartItems as $key => $cartItem) {
            $skuIndex[$cartItem->getId()] = $key;
        }

        return $skuIndex;
    }

}
