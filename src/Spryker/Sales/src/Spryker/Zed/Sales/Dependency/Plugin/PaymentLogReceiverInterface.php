<?php

/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace Spryker\Zed\Sales\Dependency\Plugin;

use Propel\Runtime\Collection\ObjectCollection;

interface PaymentLogReceiverInterface
{

    /**
     * @param ObjectCollection $orders
     *
     * @return array
     */
    public function getPaymentLogs(ObjectCollection $orders);

}
