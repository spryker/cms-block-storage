<?php
/**
 * Copyright © 2017-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Calculation\Business\Model\Calculator;

use ArrayObject;
use Generated\Shared\Transfer\CalculableObjectTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Spryker\Zed\Calculation\Business\Model\Calculator\CalculatorInterface;

class SubtotalCalculator implements CalculatorInterface
{

    /**
     * @param \Generated\Shared\Transfer\CalculableObjectTransfer $calculableObjectTransfer
     *
     * @return void
     */
    public function recalculate(CalculableObjectTransfer $calculableObjectTransfer)
    {
        $calculableObjectTransfer->requireTotals();

        $subtotal = $this->calculateTotalItemSumAggregation($calculableObjectTransfer->getItems());

        $calculableObjectTransfer->getTotals()->setSubtotal($subtotal);
    }

    /**
     * @param \ArrayObject|\Generated\Shared\Transfer\ItemTransfer[] $items
     *
     * @return int
     */
    protected function calculateTotalItemSumAggregation(ArrayObject $items)
    {
        $subtotal = 0;
        foreach ($items as $itemTransfer) {
            $itemTransfer->requireSumSubtotalAggregation();

            $subtotal += $itemTransfer->getSumSubtotalAggregation();
        }
        return $subtotal;
    }
}
