<?php
/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace SprykerFeature\Zed\PayoneOmsConnector\Communication\Plugin\Condition;

use SprykerFeature\Zed\Sales\Persistence\Propel\SpySalesOrderItem;
use Generated\Shared\Transfer\OrderTransfer;

class CaptureIsErrorPlugin extends AbstractPlugin
{
    /**
     * @param OrderTransfer $orderTransfer
     *
     * @return bool
     */
    protected function callFacade(OrderTransfer $orderTransfer)
    {
        return $this->getDependencyContainer()->createPayoneFacade()->isCaptureError($orderTransfer);
    }
}
