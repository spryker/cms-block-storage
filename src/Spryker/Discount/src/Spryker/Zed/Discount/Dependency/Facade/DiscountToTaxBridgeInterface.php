<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Discount\Dependency\Facade;

interface DiscountToTaxBridgeInterface
{

    /**
     * @param int $grossPrice
     * @param int $taxRate
     *
     * @return int
     */
    public function getTaxAmountFromGrossPrice($grossPrice, $taxRate);

    /**
     * @api
     *
     * @param int $grossPrice
     * @param float $taxRate
     *
     * @return int
     */
    public function getAccruedTaxAmountFromGrossPrice($grossPrice, $taxRate);

}
