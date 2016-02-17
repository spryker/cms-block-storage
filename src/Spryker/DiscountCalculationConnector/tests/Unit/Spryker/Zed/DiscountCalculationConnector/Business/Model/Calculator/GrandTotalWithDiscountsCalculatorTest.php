<?php
/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace Functional\Spryker\Zed\DiscountCalculationConnector\Business\Model\Calculator;

use Generated\Shared\Transfer\DiscountTotalsTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\TotalsTransfer;
use Spryker\Zed\DiscountCalculationConnector\Business\Model\Calculator\GrandTotalWithDiscountsCalculator;

class GrandTotalWithDiscountsCalculatorTest extends \PHPUnit_Framework_TestCase
{
    const GRAND_TOTAL_BEFORE_DISCOUNTS = 500;
    const DISCOUNT_AMOUNT = 100;
    const DISCOUNT_OVER_AMOUNT = 600;

    /**
     * @return void
     */
    public function testGrandTotalWithDiscountsWhenDiscountsPresentShouldBeSubtracted()
    {
        $quoteTransfer = $this->createQuoteTransferWithFixtureData(
            self::GRAND_TOTAL_BEFORE_DISCOUNTS,
            self::DISCOUNT_AMOUNT
        );

        $grandTotalWithDiscountsCalculator = $this->createGrandTotalWithDiscountsCalculator();
        $grandTotalWithDiscountsCalculator->recalculate($quoteTransfer);

        $this->assertEquals(
            self::GRAND_TOTAL_BEFORE_DISCOUNTS - self::DISCOUNT_AMOUNT,
            $quoteTransfer->getTotals()->getGrandTotal()
        );

    }

    /**
     * @return void
     */
    public function testGrandTotalWithDiscountsWhenDiscountBiggerShouldUseZero()
    {
        $quoteTransfer = $this->createQuoteTransferWithFixtureData(
            self::GRAND_TOTAL_BEFORE_DISCOUNTS,
            self::DISCOUNT_OVER_AMOUNT
        );

        $grandTotalWithDiscountsCalculator = $this->createGrandTotalWithDiscountsCalculator();
        $grandTotalWithDiscountsCalculator->recalculate($quoteTransfer);

        $this->assertEquals(0, $quoteTransfer->getTotals()->getGrandTotal());

    }


    /**
     * @return void
     */
    public function testGrandTotalWithDiscountsWhenTotalsNotPresentShouldThrowAssertException()
    {
        $this->setExpectedException('Spryker\Shared\Transfer\Exception\RequiredTransferPropertyException');

        $quoteTransfer = $this->createQuoteTransfer();

        $grandTotalWithDiscountsCalculator = $this->createGrandTotalWithDiscountsCalculator();
        $grandTotalWithDiscountsCalculator->recalculate($quoteTransfer);

    }

    /**
     * @return \Spryker\Zed\DiscountCalculationConnector\Business\Model\Calculator\GrandTotalWithDiscountsCalculator
     */
    protected function createGrandTotalWithDiscountsCalculator()
    {
         return new GrandTotalWithDiscountsCalculator();
    }

    /**
     * @param int $grandTotalBeforeDiscounts
     * @param int $discountAmount
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    protected function createQuoteTransferWithFixtureData($grandTotalBeforeDiscounts, $discountAmount)
    {
        $quoteTransfer = $this->createQuoteTransfer();

        $totalsTransfer = $this->createTotalsTransfer();
        $totalsTransfer->setGrandTotal($grandTotalBeforeDiscounts);

        $discountTotalTransfer = new DiscountTotalsTransfer();
        $discountTotalTransfer->setTotalAmount($discountAmount);

        $totalsTransfer->setDiscount($discountTotalTransfer);

        $quoteTransfer->setTotals($totalsTransfer);

        return $quoteTransfer;
    }

    /**
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    protected function createQuoteTransfer()
    {
        return new QuoteTransfer();
    }

    /**
     * @return \Generated\Shared\Transfer\TotalsTransfer
     */
    protected function createTotalsTransfer()
    {
        return new TotalsTransfer();
    }
}
