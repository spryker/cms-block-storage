<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Calculation\Dependency\Plugin;

use Spryker\Zed\Calculation\Business\Model\CalculableInterface;

interface CalculatorPluginInterface
{

    /**
     * @param \Spryker\Zed\Calculation\Business\Model\CalculableInterface $calculableContainer
     */
    public function recalculate(CalculableInterface $calculableContainer);

}
