<?php

/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace Spryker\Zed\ItemGrouperCheckoutConnector\Dependency\Facade;

use Spryker\Zed\ItemGrouper\Business\ItemGrouperFacade;
use Generated\Shared\Transfer\GroupableContainerTransfer;

class ItemGrouperCheckoutConnectorToItemGrouperBridge implements ItemGrouperCheckoutConnectorToItemGrouperInterface
{

    /**
     * @var ItemGrouperFacade
     */
    protected $itemGrouperFacade;

    /**
     * @param \Spryker\Zed\ItemGrouper\Business\ItemGrouperFacade $itemGrouperFacade
     */
    public function __construct($itemGrouperFacade)
    {
        $this->itemGrouperFacade = $itemGrouperFacade;
    }

    /**
     * @param \Generated\Shared\Transfer\GroupableContainerTransfer $groupAbleItems
     *
     * @return \Generated\Shared\Transfer\GroupableContainerTransfer
     */
    public function groupItemsByKey(GroupableContainerTransfer $groupAbleItems)
    {
        return $this->itemGrouperFacade->groupItemsByKey($groupAbleItems);
    }

}
