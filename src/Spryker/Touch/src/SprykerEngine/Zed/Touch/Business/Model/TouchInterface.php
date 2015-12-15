<?php

/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace Spryker\Zed\Touch\Business\Model;

use Generated\Shared\Transfer\TouchTransfer;

interface TouchInterface
{

    /**
     * @param string $itemType
     *
     * @return TouchTransfer[]
     */
    public function getItemsByType($itemType);

    /**
     * @param string $itemType
     * @param string $itemEvent
     * @param array $itemIds
     *
     * @return int
     */
    public function bulkUpdateTouchRecords($itemType, $itemEvent, array $itemIds = []);

}
