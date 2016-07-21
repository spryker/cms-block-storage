<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\DiscountCalculationConnector\Business\Model\Calculator;

use Generated\Shared\Transfer\CalculatedDiscountTransfer;
use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\QuoteTransfer;

class SumGrossCalculatedDiscountAmountCalculator implements CalculatorInterface
{

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return void
     */
    public function recalculate(QuoteTransfer $quoteTransfer)
    {
        $this->calculateItemGrossAmounts($quoteTransfer);
        $this->setExpenseGrossAmounts($quoteTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\ItemTransfer $itemTransfer
     *
     * @return int
     */
    protected function setItemGrossAmounts(ItemTransfer $itemTransfer)
    {
        $this->setCalculatedDiscountsSumGrossAmount($itemTransfer->getCalculatedDiscounts());

        $totalDiscountUnitGrossAmount = $this->getCalculatedDiscountsUnitGrossAmount($itemTransfer->getCalculatedDiscounts());
        if ($totalDiscountUnitGrossAmount > $itemTransfer->getUnitGrossPrice()) {
            $totalDiscountUnitGrossAmount = $itemTransfer->getUnitGrossPrice();
        }

        $totalDiscountSumGrossAmount = $this->getCalculatedDiscountsSumGrossAmount($itemTransfer->getCalculatedDiscounts());
        if ($totalDiscountSumGrossAmount > $itemTransfer->getSumGrossPrice()) {
            $totalDiscountSumGrossAmount = $itemTransfer->getSumGrossPrice();
        }

        $itemTransfer->setUnitTotalDiscountAmount($totalDiscountUnitGrossAmount);
        $itemTransfer->setSumTotalDiscountAmount($totalDiscountSumGrossAmount);

        $itemTransfer->setUnitGrossPriceWithDiscounts(
            $itemTransfer->getUnitGrossPrice() - $totalDiscountUnitGrossAmount
        );

        $itemTransfer->setSumGrossPriceWithDiscounts(
            $itemTransfer->getSumGrossPrice() - $totalDiscountSumGrossAmount
        );

    }

    /**
     * @param \ArrayObject|\Generated\Shared\Transfer\CalculatedDiscountTransfer[] $calculatedDiscounts
     *
     * @return int
     */
    protected function getCalculatedDiscountsSumGrossAmount(\ArrayObject $calculatedDiscounts)
    {
        $totalDiscountSumGrossAmount = 0;
        foreach ($calculatedDiscounts as $calculatedDiscountTransfer) {
            $totalDiscountSumGrossAmount += $calculatedDiscountTransfer->getSumGrossAmount();
        }

        return $totalDiscountSumGrossAmount;
    }

    /**
     * @param \ArrayObject|\Generated\Shared\Transfer\CalculatedDiscountTransfer[] $calculatedDiscounts
     *
     * @return int
     */
    protected function getCalculatedDiscountsUnitGrossAmount(\ArrayObject $calculatedDiscounts)
    {
        $totalDiscountUnitGrossAmount = 0;
        foreach ($calculatedDiscounts as $calculatedDiscountTransfer) {
            $totalDiscountUnitGrossAmount += $calculatedDiscountTransfer->getUnitGrossAmount();
        }

        return $totalDiscountUnitGrossAmount;
    }

    /**
     * @param \ArrayObject|\Generated\Shared\Transfer\CalculatedDiscountTransfer[] $calculatedDiscounts
     *
     * @return void
     */
    protected function setCalculatedDiscountsSumGrossAmount(\ArrayObject $calculatedDiscounts)
    {
        foreach ($calculatedDiscounts as $calculatedDiscountTransfer) {
            $this->assertCalculatedDiscountRequirements($calculatedDiscountTransfer);
            $calculatedDiscountTransfer->setSumGrossAmount(
                $calculatedDiscountTransfer->getUnitGrossAmount() * $calculatedDiscountTransfer->getQuantity()
            );
        }
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return void
     */
    protected function setExpenseGrossAmounts(QuoteTransfer $quoteTransfer)
    {
        foreach ($quoteTransfer->getExpenses() as $expenseTransfer) {
            $this->setCalculatedDiscountsSumGrossAmount($expenseTransfer->getCalculatedDiscounts());
            $unitAmount = $this->getCalculatedDiscountsUnitGrossAmount($expenseTransfer->getCalculatedDiscounts());
            $sumAmount = $this->getCalculatedDiscountsSumGrossAmount($expenseTransfer->getCalculatedDiscounts());

            $expenseTransfer->setUnitTotalDiscountAmount($unitAmount);
            $expenseTransfer->setSumTotalDiscountAmount($sumAmount);

            $expenseTransfer->setUnitGrossPriceWithDiscounts(
                $expenseTransfer->getUnitGrossPrice() - $unitAmount
            );

            $expenseTransfer->setSumGrossPriceWithDiscounts(
                $expenseTransfer->getSumGrossPrice() - $sumAmount
            );
        }
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return void
     */
    protected function calculateItemGrossAmounts(QuoteTransfer $quoteTransfer)
    {
        foreach ($quoteTransfer->getItems() as $itemTransfer) {
            $this->setItemGrossAmounts($itemTransfer);
        }
    }

    /**
     * @param \Generated\Shared\Transfer\CalculatedDiscountTransfer $calculatedDiscountTransfer
     *
     * @return void
     */
    protected function assertCalculatedDiscountRequirements(CalculatedDiscountTransfer $calculatedDiscountTransfer)
    {
        $calculatedDiscountTransfer->requireQuantity()->requireUnitGrossAmount();
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return void
     */
    protected function assertDiscountTotalRequirements(QuoteTransfer $quoteTransfer)
    {
        $quoteTransfer->requireTotals();
    }

}
