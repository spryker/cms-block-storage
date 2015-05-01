<?php

namespace SprykerFeature\Zed\Availability\Dependency\Facade;

interface AvailabilityToOmsFacadeInterface
{
    /**
     * @param string $sku
     * @return \SprykerFeature\Zed\Sales\Persistence\Propel\SpySalesOrderItem
     */
    public function countReservedOrderItemsForSku($sku);

}
