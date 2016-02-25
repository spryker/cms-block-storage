<?php
/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace Spryker\Zed\SalesAggregator\Dependency\Plugin;

use Generated\Shared\Transfer\OrderTransfer;

interface OrderTotalsAggregatePluginInterface
{

    /**
     * Aggregates data and adds it to the transfer.
     *
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return void
     */
    public function aggregate(OrderTransfer $orderTransfer);

}
