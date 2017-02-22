<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Cart\Communication\Controller;

use Generated\Shared\Transfer\CartChangeTransfer;
use Spryker\Zed\ZedRequest\Communication\Controller\AbstractGatewayController;

/**
 * @method \Spryker\Zed\Cart\Business\CartFacade getFacade()
 */
class GatewayController extends AbstractGatewayController
{

    /**
     * @param \Generated\Shared\Transfer\CartChangeTransfer $cartChangeTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    public function addItemAction(CartChangeTransfer $cartChangeTransfer)
    {
        return $this->getFacade()->add($cartChangeTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\CartChangeTransfer $cartChangeTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    public function removeItemAction(CartChangeTransfer $cartChangeTransfer)
    {
        return $this->getFacade()->remove($cartChangeTransfer);
    }

}
