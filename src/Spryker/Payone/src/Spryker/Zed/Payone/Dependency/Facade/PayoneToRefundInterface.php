<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Payone\Dependency\Facade;

use Generated\Shared\Transfer\OrderTransfer;

interface PayoneToRefundInterface
{

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return int
     */
    public function calculateRefundableAmount(OrderTransfer $orderTransfer);

}
