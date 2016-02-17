<?php

/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace Spryker\Zed\Calculation\Communication\Plugin;

use Generated\Shared\Transfer\QuoteTransfer;
use Spryker\Zed\Calculation\Business\CalculationFacade;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Spryker\Zed\Calculation\Dependency\Plugin\CalculatorPluginInterface;

/**
 * @method \Spryker\Zed\Calculation\Business\CalculationFacade getFacade()
 */
class GrandTotalTotalsCalculatorPlugin extends AbstractPlugin implements CalculatorPluginInterface
{

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return void
     */
    public function recalculate(QuoteTransfer $quoteTransfer)
    {
        $this->getFacade()->calculateGrandTotalTotals($quoteTransfer);
    }

}
