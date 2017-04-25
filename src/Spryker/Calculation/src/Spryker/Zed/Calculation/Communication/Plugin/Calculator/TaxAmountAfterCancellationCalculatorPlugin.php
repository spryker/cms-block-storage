<?php
/**
 * Copyright © 2017-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Calculation\Communication\Plugin\Calculator;


use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\CalculableObjectTransfer;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Spryker\Zed\Calculation\Dependency\Plugin\CalculationPluginInterface;

/**
 * @method \Spryker\Zed\Calculation\Business\CalculationFacade getFacade()
 * @method \Spryker\Zed\Calculation\Communication\CalculationCommunicationFactory getFactory()
 */
class TaxAmountAfterCancellationCalculatorPlugin extends AbstractPlugin implements CalculationPluginInterface
{

    /**
     * @api
     *
     * @param \Generated\Shared\Transfer\CalculableObjectTransfer $calculableObjectTransfer
     *
     * @return void
     */
    public function recalculate(CalculableObjectTransfer $calculableObjectTransfer)
    {
        $this->getFacade()
            ->calculateTaxAfterCancellation($calculableObjectTransfer);
    }
}
