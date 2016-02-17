<?php

namespace Spryker\Zed\Cart\Communication\Plugin;

use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\CartChangeTransfer;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Spryker\Zed\Cart\Dependency\ItemExpanderPluginInterface;

/**
 * @method \Spryker\Zed\Cart\Business\CartFacade getFacade()
 * @method \Spryker\Zed\Cart\Communication\CartCommunicationFactory getFactory()
 */
class SkuGroupKeyPlugin extends AbstractPlugin implements ItemExpanderPluginInterface
{

    /**
     * @param \Generated\Shared\Transfer\CartChangeTransfer $cartChangeTransfer
     *
     * @return \Generated\Shared\Transfer\CartChangeTransfer
     */
    public function expandItems(CartChangeTransfer $cartChangeTransfer)
    {
        foreach ($cartChangeTransfer->getItems() as $cartItem) {
            $cartItem->setGroupKey($this->buildGroupKey($cartItem));
        }

        return $cartChangeTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\ItemTransfer $cartItem
     *
     * @return string
     */
    protected function buildGroupKey(ItemTransfer $cartItem)
    {
        $groupKey = $cartItem->getGroupKey();
        if (empty($groupKey)) {
            return $cartItem->getSku();
        }

        return $groupKey;
    }

}
