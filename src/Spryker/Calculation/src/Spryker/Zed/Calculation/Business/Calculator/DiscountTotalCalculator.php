<?php
/**
 * Copyright © 2017-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Calculation\Business\Calculator;

use ArrayObject;
use Generated\Shared\Transfer\CalculableObjectTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Spryker\Zed\Calculation\Business\Calculator\CalculatorInterface;

class DiscountTotalCalculator implements CalculatorInterface
{

    /**
     * @param \Generated\Shared\Transfer\CalculableObjectTransfer $calculableObjectTransfer
     *
     * @return void
     */
    public function recalculate(CalculableObjectTransfer $calculableObjectTransfer)
    {
        $calculableObjectTransfer->requireTotals();

        $totalDiscountAmount = $this->calculateItemTotalDiscountAmount($calculableObjectTransfer->getItems());
        $totalDiscountAmount += $this->calculateExpenseTotalDiscountAmount($calculableObjectTransfer);

        $calculableObjectTransfer->getTotals()->setDiscountTotal($totalDiscountAmount);
    }

    /**
     * @param \Generated\Shared\Transfer\CalculableObjectTransfer $calculableObjectTransfer
     *
     * @return int
     */
    protected function calculateExpenseTotalDiscountAmount(CalculableObjectTransfer $calculableObjectTransfer)
    {
        $totalDiscountAmount = 0;
        foreach ($calculableObjectTransfer->getExpenses() as $expenseTransfer) {
            $totalDiscountAmount += $expenseTransfer->getSumDiscountAmountAggregation();
        }
        return $totalDiscountAmount;
    }

    /**
     * @param \ArrayObject|\Generated\Shared\Transfer\ItemTransfer[] $items
     *
     * @return int
     */
    protected function calculateItemTotalDiscountAmount(ArrayObject $items)
    {
        $totalDiscountAmount = 0;
        foreach ($items as $itemTransfer) {
            $totalDiscountAmount += $itemTransfer->getSumDiscountAmountFullAggregation();
        }
        return $totalDiscountAmount;
    }
}
