<?php

namespace Functional\SprykerFeature\Zed\DiscountCalculationConnector\Business\Model\Calculator;

use Codeception\TestCase\Test;
use Generated\Shared\Transfer\DiscountTotalsTransfer;
use Generated\Shared\Transfer\ExpenseTotalsTransfer;
use Generated\Shared\Transfer\TotalsTransfer;
use Generated\Zed\Ide\AutoCompletion;
use Generated\Shared\Transfer\CalculationDiscountTransfer;
use SprykerEngine\Shared\Kernel\LocatorLocatorInterface;
use SprykerFeature\Shared\Sales\Code\ExpenseConstants;
use Generated\Shared\Transfer\OrderTransfer;
use Generated\Shared\Transfer\OrderItemTransfer;
use Generated\Shared\Transfer\ExpenseTransfer;
use SprykerFeature\Zed\DiscountCalculationConnector\Business\Model\Calculator\DiscountTotalsCalculator;
use SprykerFeature\Zed\DiscountCalculationConnector\Business\Model\Calculator\GrandTotalWithDiscountsTotalsCalculator;
use SprykerEngine\Zed\Kernel\Locator;

/**
 * Class GrandTotalTest
 * @group GrandTotalTest
 * @group Calculation
 * @package PhpUnit\SprykerFeature\Zed\Calculation\Business\Model\Calculator
 */
class GrandTotalWithDiscountTest extends Test
{
    const ITEM_GROSS_PRICE = 10000;
    const ITEM_COUPON_DISCOUNT_AMOUNT = 1000;
    const ITEM_SALESRULE_DISCOUNT_AMOUNT = 1000;
    const ORDER_SHIPPING_COSTS = 2000;
    const EXPENSE_NAME_SHIPPING_COSTS = 'Shipping Costs';

    /**
     * @var LocatorLocatorInterface|AutoCompletion
     */
    protected $locator;

    protected function setUp()
    {
        parent::setUp();
        $this->locator = Locator::getInstance();
    }

    public function testGrandTotalShouldBeZeroForAnEmptyOrder()
    {
        $order = $this->getOrderWithFixtureData();

        $totalsTransfer = $this->getTotals();
        $calculator = $this->getCalculator();
        $calculator->recalculateTotals($totalsTransfer, $order, $order->getItems());

        $this->assertEquals(0, $totalsTransfer->getGrandTotalWithDiscounts());
    }

    public function testGrandTotalShouldBeModeThanZeroForAnOrderWithOneItem()
    {
        $order = $this->getOrderWithFixtureData();

        $item = $this->getItemWithFixtureData();
        $item->setGrossPrice(self::ITEM_GROSS_PRICE);

        $order->addItem($item);

        $totalsTransfer = $this->getTotals();
        $calculator = $this->getCalculator();
        $calculator->recalculateTotals($totalsTransfer, $order, $order->getItems());

        $this->assertEquals(self::ITEM_GROSS_PRICE, $totalsTransfer->getGrandTotalWithDiscounts());
    }

    public function testGrandTotalShouldReturnTwiceTheItemGrossPriceForAnOrderWithTwoItems()
    {
        $order = $this->getOrderWithFixtureData();

        $item = $this->getItemWithFixtureData();
        $item->setGrossPrice(self::ITEM_GROSS_PRICE);
        $order->addItem($item);
        $order->addItem(clone $item);

        $totalsTransfer = $this->getTotals();
        $calculator = $this->getCalculator();
        $calculator->recalculateTotals($totalsTransfer, $order, $order->getItems());

        $this->assertEquals(2 * self::ITEM_GROSS_PRICE, $totalsTransfer->getGrandTotalWithDiscounts());
    }

    public function testGrandTotalShouldReturnTheItemGrossPriceAndShippingCostsForAnOrderWithOneItemAndExpenseOnOrder()
    {
        $order = $this->getOrderWithFixtureData();

        $item = $this->getItemWithFixtureData();
        $item->setGrossPrice(self::ITEM_GROSS_PRICE);
        $order->addItem($item);

        $expense = $this->getExpenseWithFixtureData();
        $expense->setName(self::EXPENSE_NAME_SHIPPING_COSTS)
            ->setType(ExpenseConstants::EXPENSE_SHIPPING)
            ->setPriceToPay(self::ORDER_SHIPPING_COSTS)
            ->setGrossPrice(self::ORDER_SHIPPING_COSTS)
        ;

        $order->addExpense($expense);

        $totalsTransfer = $this->getTotals();
        $calculator = $this->getCalculator();
        $calculator->recalculateTotals($totalsTransfer, $order, $order->getItems());

        $this->assertEquals(self::ITEM_GROSS_PRICE + self::ORDER_SHIPPING_COSTS, $totalsTransfer->getGrandTotalWithDiscounts());
    }

    public function testGrandTotalShouldReturnTheItemGrossPriceAndShippingCostsForAnOrderWithTwoItemsAndExpenseOnItem()
    {
        $order = $this->getOrderWithFixtureData();

        $item = $this->getItemWithFixtureData();
        $item->setGrossPrice(self::ITEM_GROSS_PRICE);

        $expense = $this->getExpenseWithFixtureData();
        $expense->setName(self::EXPENSE_NAME_SHIPPING_COSTS)
            ->setType(ExpenseConstants::EXPENSE_SHIPPING)
            ->setPriceToPay(self::ORDER_SHIPPING_COSTS / 2)
            ->setGrossPrice(self::ORDER_SHIPPING_COSTS / 2);
        $item->addExpense($expense);

        $order->addItem($item);
        $order->addItem(clone $item);

        $totalsTransfer = $this->getTotals();
        $calculator = $this->getCalculator();
        $calculator->recalculateTotals($totalsTransfer, $order, $order->getItems());

        $this->assertEquals(2 * self::ITEM_GROSS_PRICE + self::ORDER_SHIPPING_COSTS, $totalsTransfer->getGrandTotalWithDiscounts());
    }

    public function testGrandTotalShouldReturnTheItemGrossPriceMinusTheItemCouponDiscountForAnOrderWithOneItemAndACouponDiscount()
    {
        $order = $this->getOrderWithFixtureData();

        $item = $this->getItemWithFixtureData();
        $item->setGrossPrice(self::ITEM_GROSS_PRICE);

        $discount = $this->getDiscount();
        $discount->setAmount(self::ITEM_COUPON_DISCOUNT_AMOUNT);

        $item->addDiscount($discount);

        $order->addItem($item);

        $totalsTransfer = $this->getTotals();
        $calculator = $this->getCalculator();
        $calculator->recalculateTotals($totalsTransfer, $order, $order->getItems());

        $this->assertEquals(
            self::ITEM_GROSS_PRICE - self::ITEM_COUPON_DISCOUNT_AMOUNT,
            $totalsTransfer->getGrandTotalWithDiscounts()
        );
    }

    public function testGrandTotalShouldReturnTheItemGrossPriceMinusTheItemSalesurleDiscountForAnOrderWithTwoItemsAndADiscountAndACouponDiscount()
    {
        $order = $this->getOrderWithFixtureData();

        $item = $this->getItemWithFixtureData();
        $item->setGrossPrice(self::ITEM_GROSS_PRICE);

        $discount = $this->getDiscount();
        $discount->setAmount(self::ITEM_COUPON_DISCOUNT_AMOUNT);
        $item->addDiscount($discount);

        $discount = $this->getDiscount();
        $discount->setAmount(self::ITEM_SALESRULE_DISCOUNT_AMOUNT);
        $item->addDiscount($discount);

        $order->addItem($item);
        $order->addItem(clone $item);

        $totalsTransfer = $this->getTotals();
        $calculator = $this->getCalculator();
        $calculator->recalculateTotals($totalsTransfer, $order, $order->getItems());

        $this->assertEquals(
            2 * (self::ITEM_GROSS_PRICE - self::ITEM_SALESRULE_DISCOUNT_AMOUNT- self::ITEM_COUPON_DISCOUNT_AMOUNT),
            $totalsTransfer->getGrandTotalWithDiscounts()
        );
    }

    /**
     * @return GrandTotalWithDiscountsTotalsCalculator
     */
    protected function getCalculator()
    {
        return new GrandTotalWithDiscountsTotalsCalculator(
            $this->locator->calculation()->facade(),
            new DiscountTotalsCalculator($this->locator)
        );
    }

    /**
     * @return TotalsTransfer
     */
    protected function getTotals()
    {
        $totals = new TotalsTransfer();
        $totals->setDiscount(new DiscountTotalsTransfer());
        $totals->setExpenses(new ExpenseTotalsTransfer());

        return $totals;
    }

    /**
     * @return CalculationDiscountTransfer
     */
    protected function getDiscount()
    {
        return new CalculationDiscountTransfer();
    }

    /**
     * @return OrderTransfer
     */
    protected function getOrderWithFixtureData()
    {
        $order = new OrderTransfer();
        $totals = new TotalsTransfer();
        $totals->setDiscount(new DiscountTotalsTransfer());
        $order->setTotals($totals);
        $order->setDiscounts(new CalculationDiscountTransfer());

        return $order;
    }

    /**
     * @return OrderItemTransfer
     */
    protected function getItemWithFixtureData()
    {
        $item = new OrderItemTransfer();

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
}
