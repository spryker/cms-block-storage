<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Stock\Business\Model;

use Generated\Shared\Transfer\StockProductTransfer;
use Generated\Shared\Transfer\TypeTransfer;

interface WriterInterface
{

    /**
     * @param \Generated\Shared\Transfer\TypeTransfer $stockTypeTransfer
     *
     * @return int
     */
    public function createStockType(TypeTransfer $stockTypeTransfer);

    /**
     * @param \Generated\Shared\Transfer\TypeTransfer $stockTypeTransfer
     *
     * @return int
     */
    public function updateStockType(TypeTransfer $stockTypeTransfer);

    /**
     * @param \Generated\Shared\Transfer\StockProductTransfer $transferStockProduct
     *
     * @return int
     */
    public function updateStockProduct(StockProductTransfer $transferStockProduct);

    /**
     * @param string $sku
     * @param int $decrementBy
     * @param string $stockType
     *
     * @return void
     */
    public function decrementStock($sku, $stockType, $decrementBy = 1);

    /**
     * @param string $sku
     * @param int $incrementBy
     * @param string $stockType
     *
     * @return void
     */
    public function incrementStock($sku, $stockType, $incrementBy = 1);

    /**
     * @param \Generated\Shared\Transfer\StockProductTransfer $transferStockProduct
     *
     * @return int
     */
    public function createStockProduct(StockProductTransfer $transferStockProduct);

}
