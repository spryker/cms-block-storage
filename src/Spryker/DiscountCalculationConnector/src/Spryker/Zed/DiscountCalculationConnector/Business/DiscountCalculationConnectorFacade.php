<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\DiscountCalculationConnector\Business;

use Generated\Shared\Transfer\QuoteTransfer;
use Spryker\Zed\Kernel\Business\AbstractFacade;

/**
 * @method \Spryker\Zed\DiscountCalculationConnector\Business\DiscountCalculationConnectorBusinessFactory getFactory()
 */
class DiscountCalculationConnectorFacade extends AbstractFacade
{

    /**
     * @api
     *
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return void
     */
    public function calculateDiscountTotals(QuoteTransfer $quoteTransfer)
    {
        $this->getFactory()->createDiscountTotalsCalculator()->recalculate($quoteTransfer);
    }

    /**
     * @api
     *
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return void
     */
    public function removeAllCalculatedDiscounts(QuoteTransfer $quoteTransfer)
    {
        $this->getFactory()->createRemoveAllCalculatedDiscountsCalculator()->recalculate($quoteTransfer);
    }

    /**
     * @api
     *
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return void
     */
    public function calculateGrandTotalWithDiscounts(QuoteTransfer $quoteTransfer)
    {
        $this->getFactory()->createGrandTotalWithDiscountsCalculator()->recalculate($quoteTransfer);
    }

    /**
     * @api
     *
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return void
     */
    public function calculateSumGrossCalculatedDiscountAmount(QuoteTransfer $quoteTransfer)
    {
        $this->getFactory()->createSumGrossCalculatedDiscountAmountCalculator()->recalculate($quoteTransfer);
    }

}
