<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductOptionCartConnector\Business\Model;

use Generated\Shared\Transfer\CartChangeTransfer;
use Generated\Shared\Transfer\ItemTransfer;
use Spryker\Zed\ProductOptionCartConnector\Dependency\Facade\ProductOptionCartConnectorToProductOptionInterface;

class ProductOptionValueExpander implements ProductOptionValueExpanderInterface
{

    /**
     * @var \Spryker\Zed\ProductOptionCartConnector\Dependency\Facade\ProductOptionCartConnectorToProductOptionInterface
     */
    protected $productOptionFacade;

    /**
     * @param \Spryker\Zed\ProductOptionCartConnector\Dependency\Facade\ProductOptionCartConnectorToProductOptionInterface $productOptionFacade
     */
    public function __construct(ProductOptionCartConnectorToProductOptionInterface $productOptionFacade)
    {
        $this->productOptionFacade = $productOptionFacade;
    }

    /**
     * @param \Generated\Shared\Transfer\CartChangeTransfer $changeTransfer
     *
     * @return \Generated\Shared\Transfer\CartChangeTransfer
     */
    public function expandProductOptions(CartChangeTransfer $changeTransfer)
    {
        foreach ($changeTransfer->getItems() as $itemTransfer) {
            $this->expandProductOptionTransfers($itemTransfer);
        }

        return $changeTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\ItemTransfer $itemTransfer
     *
     * @throws \RuntimeException
     * @return void
     */
    protected function expandProductOptionTransfers(ItemTransfer $itemTransfer)
    {
        foreach ($itemTransfer->getProductOptions() as &$productOptionTransfer) {
            $productOptionTransfer->requireIdProductOptionValue();

            $productOptionTransfer = $this->productOptionFacade->getProductOptionValue(
                $productOptionTransfer->getIdProductOptionValue()
            );
        }
    }

}
