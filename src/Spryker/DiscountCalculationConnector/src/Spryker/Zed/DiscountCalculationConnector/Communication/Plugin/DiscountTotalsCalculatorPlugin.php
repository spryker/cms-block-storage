<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\DiscountCalculationConnector\Communication\Plugin;

use Generated\Shared\Transfer\TotalsTransfer;
use Spryker\Zed\Calculation\Business\Model\CalculableInterface;
use Spryker\Zed\Calculation\Dependency\Plugin\TotalsCalculatorPluginInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;

/**
 * @method \Spryker\Zed\DiscountCalculationConnector\Communication\DiscountCalculationConnectorCommunicationFactory getFactory()
 * @method \Spryker\Zed\DiscountCalculationConnector\Business\DiscountCalculationConnectorFacade getFacade()
 */
class DiscountTotalsCalculatorPlugin extends AbstractPlugin implements TotalsCalculatorPluginInterface
{

    /**
     * @param \Generated\Shared\Transfer\TotalsTransfer $totalsTransfer
     * @param \Spryker\Zed\Calculation\Business\Model\CalculableInterface $calculableContainer
     * @param \ArrayObject|\Generated\Shared\Transfer\OrderItemsTransfer|\Generated\Shared\Transfer\ItemTransfer[] $calculableItems
     *
     * @return void
     */
    public function recalculateTotals(
        TotalsTransfer $totalsTransfer,
        CalculableInterface $calculableContainer,
        $calculableItems
    ) {
        $this->getFacade()->recalculateDiscountTotals($totalsTransfer, $calculableContainer, $calculableItems);
    }

}
