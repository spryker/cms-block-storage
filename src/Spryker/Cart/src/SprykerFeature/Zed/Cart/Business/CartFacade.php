<?php
/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace SprykerFeature\Zed\Cart\Business;

use SprykerEngine\Zed\Kernel\Business\AbstractFacade;
use Generated\Shared\Cart\CartInterface;
use Generated\Shared\Cart\ChangeInterface;

/**
 * @method CartDependencyContainer getDependencyContainer()
 */
class CartFacade extends AbstractFacade
{
    /**
     * @param ChangeInterface $cartChange
     *
     * @return CartInterface
     */
    public function addToCart(ChangeInterface $cartChange)
    {
        $addOperator = $this->getDependencyContainer()->createAddOperator();

        return $addOperator->executeOperation($cartChange);
    }

    /**
     * @param ChangeInterface $cartChange
     *
     * @return CartInterface
     */
    public function increaseQuantity(ChangeInterface $cartChange)
    {
        $increaseOperator = $this->getDependencyContainer()->createIncreaseOperator();

        return $increaseOperator->executeOperation($cartChange);
    }

    /**
     * @param ChangeInterface $cartChange
     *
     * @return CartInterface
     */
    public function removeFromCart(ChangeInterface $cartChange)
    {
        $removeOperator = $this->getDependencyContainer()->createRemoveOperator();

        return $removeOperator->executeOperation($cartChange);
    }

    /**
     * @param ChangeInterface $cartChange
     *
     * @return CartInterface
     */
    public function decreaseQuantity(ChangeInterface $cartChange)
    {
        $decreaseOperator = $this->getDependencyContainer()->createDecreaseOperator();

        return $decreaseOperator->executeOperation($cartChange);
    }

    /**
     * @param CartInterface $cart
     *
     * @return CartInterface
     */
    public function recalculateCart(CartInterface  $cart)
    {
        $calculator = $this->getDependencyContainer()->createCartCalculator();

        return $calculator->recalculate($cart);
    }
}
