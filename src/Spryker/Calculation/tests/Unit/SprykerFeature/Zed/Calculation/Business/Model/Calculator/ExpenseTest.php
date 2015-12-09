<?php

/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace Unit\SprykerFeature\Zed\Calculation\Business\Model\Calculator;

use Generated\Zed\Ide\AutoCompletion;
use SprykerEngine\Shared\Kernel\AbstractLocatorLocator;
use Generated\Shared\Transfer\OrderTransfer;
use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\DiscountTransfer;
use Generated\Shared\Transfer\ExpenseTransfer;
use SprykerEngine\Zed\Kernel\AbstractUnitTest;
use SprykerFeature\Zed\Calculation\Business\CalculationFacade;
use SprykerFeature\Zed\Calculation\Communication\Plugin\ExpensePriceToPayCalculatorPlugin;
use SprykerEngine\Zed\Kernel\Locator;
use SprykerFeature\Zed\Sales\Business\Model\CalculableContainer;

/**
 * @group SprykerFeature
 * @group Zed
 * @group Calculation
 * @group Business
 * @group Expense
 */
class ExpenseTest extends AbstractUnitTest
{

    const EXPENSE_1000 = 1000;
    const SALES_DISCOUNT_100 = 100;
    const SALES_DISCOUNT_50 = 50;

    /**
     * @return void
     */
    public function testExpensePriceToPayShouldBeTheSameAsTheGrossPriceForNoExpenseDiscounts()
    {
        $order = $this->getOrderWithFixtureData();

        $expense = $this->getExpenseWithFixtureData();
        $expense->setGrossPrice(self::EXPENSE_1000);

        $item = $this->getItemWithFixtureData();
        $item->addExpense($expense);

        $order->getCalculableObject()->addItem($item);

        $calculator = new ExpensePriceToPayCalculatorPlugin();
        $calculator->setOwnFacade(new CalculationFacade());
        $calculator->recalculate($order);

        foreach ($order->getCalculableObject()->getItems() as $item) {
            foreach ($item->getExpenses() as $expense) {
                $this->assertEquals($expense->getGrossPrice(), $expense->getPriceToPay());
            }
        }
    }

    /**
     * @return void
     */
    public function testExpensePriceToPayShouldBeZeroIfExpenseDiscountAmountIsExpenseGrossPrice()
    {
        $order = $this->getOrderWithFixtureData();
        $expense = $this->getExpenseWithFixtureData();
        $expense->setGrossPrice(self::EXPENSE_1000);

        $item = $this->getItemWithFixtureData();
        $item->addExpense($expense);

        $discount = $this->getPriceDiscount();
        $discount->setAmount($expense->getGrossPrice());

        $expense->addDiscountItem($discount);
        $order->getCalculableObject()->addItem($item);

        $calculator = new ExpensePriceToPayCalculatorPlugin();
        $calculator->setOwnFacade(new CalculationFacade());
        $calculator->recalculate($order);

        foreach ($order->getCalculableObject()->getItems() as $item) {
            foreach ($item->getExpenses() as $expense) {
                $this->assertEquals(0, $expense->getPriceToPay());
            }
        }
    }

    /**
     * @return void
     */
    public function testExpensePriceToPayShouldBeHalfOfTheExpenseGrossPriceForTwoDiscountsWithAQuaterOfTheGrossPriceEach()
    {
        $order = $this->getOrderWithFixtureData();
        $expense = $this->getExpenseWithFixtureData();
        $expense->setGrossPrice(self::EXPENSE_1000);

        $item = $this->getItemWithFixtureData();
        $item->addExpense($expense);

        $discount = $this->getPriceDiscount();
        $discount->setAmount($expense->getGrossPrice() / 4);

        $expense->addDiscountItem($discount);
        $expense->addDiscountItem(clone $discount);

        $order->getCalculableObject()->addItem($item);

        $calculator = new ExpensePriceToPayCalculatorPlugin();
        $calculator->setOwnFacade(new CalculationFacade());
        $calculator->recalculate($order);

        foreach ($order->getCalculableObject()->getItems() as $item) {
            foreach ($item->getExpenses() as $expense) {
                $this->assertEquals($expense->getGrossPrice() / 2, $expense->getPriceToPay());
            }
        }
    }

    /**
     * @return DiscountTransfer
     */
    protected function getPriceDiscount()
    {
        return new DiscountTransfer();
    }

    /**
     * @return CalculableContainer
     */
    protected function getOrderWithFixtureData()
    {
        $order = new OrderTransfer();

        return new CalculableContainer($order);
    }

    /**
     * @return ItemTransfer
     */
    protected function getItemWithFixtureData()
    {
        $item = new ItemTransfer();

        return $item;
    }

    /**
     * @return ExpenseTransfer
     */
    protected function getExpenseWithFixtureData()
    {
        $expense = new ExpenseTransfer();

        return $expense;
    }

    /**
     * @return AbstractLocatorLocator|AutoCompletion|Locator
     */
    private function getLocator()
    {
        return Locator::getInstance();
    }

}
