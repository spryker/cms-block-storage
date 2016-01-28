<?php

/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace Functional\Spryker\Zed\DiscountCalculationConnector\Business\Model;

use Codeception\TestCase\Test;
use Spryker\Shared\DiscountCalculationConnector\DiscountCalculationConnectorConstants;
use Generated\Shared\Transfer\DiscountItemsTransfer;
use Generated\Shared\Transfer\DiscountTotalsTransfer;
use Generated\Shared\Transfer\DiscountTransfer;
use Generated\Shared\Transfer\ExpensesTransfer;
use Generated\Shared\Transfer\TotalsTransfer;
use Generated\Shared\Transfer\OrderItemsTransfer;
use Generated\Zed\Ide\AutoCompletion;
use Spryker\Shared\Kernel\LocatorLocatorInterface;
use Spryker\Zed\Calculation\Communication\Plugin\ExpensePriceToPayCalculatorPlugin;
use Spryker\Zed\Calculation\Communication\Plugin\ExpenseTotalsCalculatorPlugin;
use Spryker\Zed\Calculation\Communication\Plugin\GrandTotalTotalsCalculatorPlugin;
use Spryker\Zed\Calculation\Communication\Plugin\ItemPriceToPayCalculatorPlugin;
use Generated\Shared\Transfer\OrderTransfer;
use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\ExpenseTransfer;
use Spryker\Zed\Calculation\Business\Model\StackExecutor;
use Spryker\Zed\DiscountCalculationConnector\Communication\Plugin\DiscountCalculatorPlugin;
use Spryker\Zed\DiscountCalculationConnector\Communication\Plugin\GrandTotalWithDiscountsTotalsCalculatorPlugin;
use Spryker\Zed\Sales\Business\Model\CalculableContainer;

/**
 * @group Spryker
 * @group Zed
 * @group DiscountCalculationConnector
 * @group Business
 * @group Calculator
 */
class CalculatorTest extends Test
{

    const ITEM_GROSS_PRICE = 10000;
    const ITEM_COUPON_DISCOUNT_AMOUNT = 1000;
    const ITEM_DISCOUNT_AMOUNT = 1000;
    const ORDER_SHIPPING_COSTS = 2000;
    const EXPENSE_NAME_SHIPPING_COSTS = 'Shipping Costs';
    const EXPENSE_TYPE_SHIPPING = 'shipping';

    /**
     * @var LocatorLocatorInterface|AutoCompletion
     */
    protected $locator;

    /**
     * @return void
     */
    protected function setUp()
    {
        parent::setUp();
    }

    protected function createCalculatorStack()
    {
        $stack = [
            new ExpensePriceToPayCalculatorPlugin(),
            new DiscountCalculatorPlugin(),
            new ExpenseTotalsCalculatorPlugin(),
            new GrandTotalTotalsCalculatorPlugin(),
            new ExpensePriceToPayCalculatorPlugin(),
            new ItemPriceToPayCalculatorPlugin(),
            new GrandTotalWithDiscountsTotalsCalculatorPlugin(),
        ];

        return $stack;
    }

    /**
     * @return void
     */
    public function testCanRecalculateAnEmptyOrder()
    {
        $order = $this->getOrderWithFixtureData();
        $calculator = $this->getCalculator();
        $calculatorStack = $this->createCalculatorStack();
        $calculator->recalculate($calculatorStack, $order);
        $this->assertEmpty($order->getCalculableObject()->getTotals()->getGrandTotalWithDiscounts());
    }

    /**
     * @return void
     */
    public function testCanRecalculateAnExampleOrderWithOneItemAndExpenseOnOrder()
    {
        $order = $this->getOrderWithFixtureData();
        $items = $this->getItemCollection();
        $item = $this->getItemWithFixtureData();
        $item->setGrossPrice(self::ITEM_GROSS_PRICE);
        $item->setQuantity(1);

        $discountCollection = $this->getPriceDiscountCollection();

        $discountTransfer = $this->getPriceDiscount();
        $discountTransfer->setAmount(self::ITEM_COUPON_DISCOUNT_AMOUNT);
        $discountCollection->addDiscount($discountTransfer);

        $discountTransfer = $this->getPriceDiscount();
        $discountTransfer->setAmount(self::ITEM_DISCOUNT_AMOUNT);
        $discountCollection->addDiscount($discountTransfer);

        $expense = $this->getExpenseWithFixtureData();
        $expense->setName(self::EXPENSE_NAME_SHIPPING_COSTS)
            ->setType(self::EXPENSE_TYPE_SHIPPING)
            ->setPriceToPay(self::ORDER_SHIPPING_COSTS)
            ->setGrossPrice(self::ORDER_SHIPPING_COSTS);

        $expensesCollection = $this->getExpenseCollection();
        $expensesCollection->addCalculationExpense($expense);
        $order->getCalculableObject()->setExpenses($expensesCollection);

        $item->setDiscounts($discountCollection);
        $items->addOrderItem($item);
        $order->getCalculableObject()->setItems($items);

        $calculator = $this->getCalculator();

        $expected = self::ORDER_SHIPPING_COSTS
            + self::ITEM_GROSS_PRICE
            - self::ITEM_COUPON_DISCOUNT_AMOUNT
            - self::ITEM_DISCOUNT_AMOUNT;

        $calculatorStack = $this->createCalculatorStack();

        $calculator->recalculate($calculatorStack, $order);
        $totals = $order->getCalculableObject()->getTotals();
        $this->assertEquals($expected, $totals->getGrandTotalWithDiscounts());

        $calculator->recalculateTotals($calculatorStack, $order);
        $totals = $order->getCalculableObject()->getTotals();
        $this->assertEquals($expected, $totals->getGrandTotalWithDiscounts());

        $items = $order->getCalculableObject()->getItems();
        $expectedItemPriceToPay = self::ITEM_GROSS_PRICE - self::ITEM_COUPON_DISCOUNT_AMOUNT - self::ITEM_DISCOUNT_AMOUNT;

        foreach ($items as $item) {
            $this->assertEquals($expectedItemPriceToPay, $item->getPriceToPay());
        }
    }

    /**
     * @return void
     */
    public function testCanRecalculateAnExampleOrderWithTwoItemsAndExpenseOnItems()
    {
        $order = $this->getOrderWithFixtureData();
//        $item = $this->getItemWithFixtureData();
//        $item->setGrossPrice(self::ITEM_GROSS_PRICE);
//
//        $discount = $this->getPriceDiscount();
//        $discount->setAmount(self::ITEM_COUPON_DISCOUNT_AMOUNT);
//        $item->addDiscount($discount);
//
//        $discount = $this->getPriceDiscount();
//        $discount->setAmount(self::ITEM_DISCOUNT_AMOUNT);
//        $item->addDiscount($discount);
//
//        $expense = $this->getExpenseWithFixtureData();
//        $expense->setName(self::EXPENSE_NAME_SHIPPING_COSTS)
//            ->setType(ExpenseConstants::EXPENSE_SHIPPING)
//            ->setPriceToPay(self::ORDER_SHIPPING_COSTS/2)
//            ->setGrossPrice(self::ORDER_SHIPPING_COSTS/2);
//
//        $item->addExpense($expense);
//
//        $order->addItem($item);
//        $order->addItem(clone $item);
//
//        $calculator = $this->getCalculator();
//        $calculatorStack = $this->createCalculatorStack();
//        $order = $calculator->recalculate($calculatorStack, $order);
//        $calculator->recalculateTotals($calculatorStack, $order);
//
//        $totals = $order->getTotals();
//        $expectedSubTotal = 2 * self::ITEM_GROSS_PRICE + self::ORDER_SHIPPING_COSTS;
//        $this->assertEquals($expectedSubTotal, $totals->getSubtotal());
//
//        $expectedGrandTotalWithDiscounts = self::ORDER_SHIPPING_COSTS + 2
//            * (self::ITEM_GROSS_PRICE - self::ITEM_COUPON_DISCOUNT_AMOUNT - self::ITEM_DISCOUNT_AMOUNT);
//        $this->assertEquals($expectedGrandTotalWithDiscounts, $totals->getGrandTotalWithDiscounts());
//
//        $items = $order->getItems();
//        $expectedItemPriceToPay = self::ORDER_SHIPPING_COSTS / 2 + self::ITEM_GROSS_PRICE
//            - self::ITEM_COUPON_DISCOUNT_AMOUNT - self::ITEM_DISCOUNT_AMOUNT;
//
//        foreach ($items as $item) {
//            $this->assertEquals($expectedItemPriceToPay, $item->getPriceToPay());
//        }

//        $order->getCalculableObject()->addItem($item);
//        $order->getCalculableObject()->addItem(clone $item);
//
//        $calculator = $this->getCalculator();
//        $calculatorStack = $this->createCalculatorStack();
//        $order = $calculator->recalculate($calculatorStack, $order);
//        $calculator->recalculateTotals($calculatorStack, $order);
//
//        $totals = $order->getCalculableObject()->getTotals();
//        $expectedSubTotal = 2 * self::ITEM_GROSS_PRICE + self::ORDER_SHIPPING_COSTS;
//        $this->assertEquals($expectedSubTotal, $totals->getSubtotal());
//
//        $expectedGrandTotalWithDiscounts = self::ORDER_SHIPPING_COSTS + 2
//            * (self::ITEM_GROSS_PRICE - self::ITEM_COUPON_DISCOUNT_AMOUNT - self::ITEM_DISCOUNT_AMOUNT);
//
//        $this->assertEquals($expectedGrandTotalWithDiscounts, $totals->getGrandTotalWithDiscounts());
//
//        $items = $order->getCalculableObject()->getItems();
//        $expectedItemPriceToPay = self::ORDER_SHIPPING_COSTS / 2 + self::ITEM_GROSS_PRICE
//            - self::ITEM_COUPON_DISCOUNT_AMOUNT - self::ITEM_DISCOUNT_AMOUNT;
//
//        foreach ($items as $item) {
//            $this->assertEquals($expectedItemPriceToPay, $item->getPriceToPay());
//        }
    }

    /**
     * @return StackExecutor
     */
    protected function getCalculator()
    {
        return new StackExecutor();
    }

    /**
     * @return OrderItemsTransfer
     */
    protected function getItemCollection()
    {
        return new OrderItemsTransfer();
    }

    /**
     * @return DiscountItemsTransfer
     */
    protected function getPriceDiscountCollection()
    {
        return new DiscountItemsTransfer();
    }

    /**
     * @return ExpensesTransfer
     */
    protected function getExpenseCollection()
    {
        return new ExpensesTransfer();
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
        $totals = new TotalsTransfer();
        $totals->setDiscount(new DiscountTotalsTransfer());
        $order->setTotals($totals);

        return new CalculableContainer($order);
    }

    /**
     * @return ItemTransfer
     */
    protected function getItemWithFixtureData()
    {
        return new ItemTransfer();
    }

    /**
     * @return ExpenseTransfer
     */
    protected function getExpenseWithFixtureData()
    {
        return new ExpenseTransfer();
    }

}
