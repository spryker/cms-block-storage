<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Availability\Dependency\Facade;

class AvailabilityToOmsBridge implements AvailabilityToOmsInterface
{

    /**
     * @var \Spryker\Zed\Oms\Business\OmsFacadeInterface
     */
    protected $omsFacade;

    /**
     * @param \Spryker\Zed\Oms\Business\OmsFacadeInterface $omsFacade
     */
    public function __construct($omsFacade)
    {
        $this->omsFacade = $omsFacade;
    }

    /**
     * @deprecated Use sumReservedProductQuantitiesForSku() instead
     *
     * @param string $sku
     *
     * @return \Orm\Zed\Sales\Persistence\SpySalesOrderItem
     */
    public function countReservedOrderItemsForSku($sku)
    {
        return $this->omsFacade->countReservedOrderItemsForSku($sku);
    }

    /**
     * @param string $sku
     *
     * @return int
     */
    public function sumReservedProductQuantitiesForSku($sku)
    {
        return $this->omsFacade->sumReservedProductQuantitiesForSku($sku);
    }

}
