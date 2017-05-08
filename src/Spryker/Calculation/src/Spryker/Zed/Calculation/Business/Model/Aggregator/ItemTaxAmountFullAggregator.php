<?php
/**
 * Copyright © 2017-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Calculation\Business\Model\Aggregator;

use ArrayObject;
use Generated\Shared\Transfer\CalculableObjectTransfer;
use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Spryker\Zed\Calculation\Business\Model\Calculator\CalculatorInterface;

class ItemTaxAmountFullAggregator implements CalculatorInterface
{

    /**
     * @param \Generated\Shared\Transfer\CalculableObjectTransfer $calculableObjectTransfer
     *
     * @return void
     */
    public function recalculate(CalculableObjectTransfer $calculableObjectTransfer)
    {
        $this->calculateTaxAmountFullAggregationForItems($calculableObjectTransfer->getItems());
    }

    /**
     * @param ItemTransfer $itemTransfer
     *
     * @return int
     */
    protected function calculateProductOptionSumTaxAmount(ItemTransfer $itemTransfer)
    {
        $productOptionSumTotalTaxAmount = 0;
        foreach ($itemTransfer->getProductOptions() as $productOptionTransfer) {
            $productOptionSumTotalTaxAmount += $productOptionTransfer->getSumTaxAmount();
        }

        return $productOptionSumTotalTaxAmount;
    }

    /**
     * @param ItemTransfer $itemTransfer
     *
     * @return int
     */
    protected function calculateProductOptionUnitTaxAmount(ItemTransfer $itemTransfer)
    {
        $productOptionUnitTotalTaxAmount = 0;
        foreach ($itemTransfer->getProductOptions() as $productOptionTransfer) {
            $productOptionUnitTotalTaxAmount += $productOptionTransfer->getUnitTaxAmount();
        }

        return $productOptionUnitTotalTaxAmount;
    }

    /**
     * @param \ArrayObject|\Generated\Shared\Transfer\ItemTransfer[] $items
     *
     * @return void
     */
    protected function calculateTaxAmountFullAggregationForItems(ArrayObject $items)
    {
        foreach ($items as $itemTransfer) {

            $productOptionUnitTaxAmount = $this->calculateProductOptionUnitTaxAmount($itemTransfer);
            $itemTransfer->setUnitTaxAmountFullAggregation($itemTransfer->getUnitTaxAmount() + $productOptionUnitTaxAmount);

            $productOptionSumTaxAmount = $this->calculateProductOptionSumTaxAmount($itemTransfer);
            $itemTransfer->setSumTaxAmountFullAggregation($itemTransfer->getSumTaxAmount() + $productOptionSumTaxAmount);

        }
    }
}
