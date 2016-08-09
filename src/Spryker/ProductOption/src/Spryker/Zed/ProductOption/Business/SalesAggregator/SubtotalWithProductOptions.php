<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductOption\Business\SalesAggregator;

use Generated\Shared\Transfer\OrderTransfer;

class SubtotalWithProductOptions implements OrderAmountAggregatorInterface
{

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return void
     */
    public function aggregate(OrderTransfer $orderTransfer)
    {
        $this->assertSubtotalWithProductOptionsRequirements($orderTransfer);

        $currentSubtotalAmount = $orderTransfer->getTotals()->getSubtotal();
        $productOptionTotalGrossSumAmount = $this->getTotalProductOptionSumGrossPrice($orderTransfer);

        $orderTransfer->getTotals()->setSubtotal($currentSubtotalAmount + $productOptionTotalGrossSumAmount);
    }

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return void
     */
    protected function assertSubtotalWithProductOptionsRequirements(OrderTransfer $orderTransfer)
    {
        $orderTransfer->requireTotals();
        $orderTransfer->getTotals()
            ->requireSubtotal();
    }

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return int
     */
    protected function getTotalProductOptionSumGrossPrice(OrderTransfer $orderTransfer)
    {
        $productOptionTotalGrossSumAmount = 0;
        foreach ($orderTransfer->getItems() as $itemTransfer) {
            foreach ($itemTransfer->getProductOptions() as $productOptionTransfer) {
                $productOptionTransfer->requireSumGrossPrice();
                $productOptionTotalGrossSumAmount += $productOptionTransfer->getSumGrossPrice();
            }
        }

        return $productOptionTotalGrossSumAmount;
    }

}
