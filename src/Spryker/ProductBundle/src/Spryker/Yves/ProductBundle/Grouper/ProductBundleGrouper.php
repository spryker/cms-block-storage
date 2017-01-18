<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Yves\ProductBundle\Grouper;

use ArrayObject;
use Generated\Shared\Transfer\ItemTransfer;

class ProductBundleGrouper implements ProductBundleGrouperInterface
{

    const BUNDLE_ITEMS = 'bundleItems';
    const BUNDLE_PRODUCT = 'bundleProduct';

    /**
     * @param \ArrayObject|\Generated\Shared\Transfer\ItemTransfer[] $items
     * @param \ArrayObject|\Generated\Shared\Transfer\ItemTransfer[] $bundleItems
     *
     * @return array
     */
    public function getGroupedBundleItems(ArrayObject $items, ArrayObject $bundleItems)
    {
        $groupedBundleQuantity = $this->getGroupedBundleQuantity($bundleItems);

        $singleItems = [];
        $groupedBundleItems = [];
        foreach ($items as $itemTransfer) {
            if (!$itemTransfer->getRelatedBundleItemIdentifier()) {
                $singleItems[] = $itemTransfer;
            }

            foreach ($bundleItems as $bundleItemTransfer) {
                if ($bundleItemTransfer->getBundleItemIdentifier() !== $itemTransfer->getRelatedBundleItemIdentifier()) {
                    continue;
                }

                $groupedBundleItems = $this->getCurrentBundle($groupedBundleItems, $bundleItemTransfer, $groupedBundleQuantity);

                $currentBundleItemTransfer = $this->getBundleProduct($groupedBundleItems, $bundleItemTransfer->getSku());
                if ($currentBundleItemTransfer->getBundleItemIdentifier() !== $itemTransfer->getRelatedBundleItemIdentifier()) {
                    continue;
                }

                $groupedBundleItems[$bundleItemTransfer->getSku()][static::BUNDLE_ITEMS] = $this->groupBundledItems(
                    $groupedBundleItems,
                    $itemTransfer,
                    $bundleItemTransfer->getSku()
                );
            }

        }

        return array_merge(
            $singleItems,
            $groupedBundleItems
        );
    }

    /**
     * @param \ArrayObject|\Generated\Shared\Transfer\ItemTransfer[] $bundleItems
     *
     * @return array
     */
    protected function getGroupedBundleQuantity(ArrayObject $bundleItems)
    {
        $groupedBundleQuantity = [];
        foreach ($bundleItems as $bundleProductTransfer) {
            if (!isset($groupedBundleQuantity[$bundleProductTransfer->getSku()])) {
                $groupedBundleQuantity[$bundleProductTransfer->getSku()] = $bundleProductTransfer->getQuantity();
            } else {
                $groupedBundleQuantity[$bundleProductTransfer->getSku()] += $bundleProductTransfer->getQuantity();
            }
        }
        return $groupedBundleQuantity;
    }

    /**
     * @param array|\Generated\Shared\Transfer\ItemTransfer[] $bundleItems
     * @param \Generated\Shared\Transfer\ItemTransfer $bundleItemTransfer
     * @param array $groupedBundleQuantity
     *
     * @return array
     */
    protected function getCurrentBundle(array $bundleItems, ItemTransfer $bundleItemTransfer, $groupedBundleQuantity)
    {
        if (isset($bundleItems[$bundleItemTransfer->getSku()])) {
            return $bundleItems;
        }

        $bundleProduct = clone $bundleItemTransfer;
        $bundleProduct->setQuantity($groupedBundleQuantity[$bundleProduct->getSku()]);

        $bundleItems[$bundleProduct->getSku()] = [
            static::BUNDLE_PRODUCT => $bundleProduct,
            static::BUNDLE_ITEMS => [],
        ];

        return $bundleItems;
    }

    /**
     * @param array|\Generated\Shared\Transfer\ItemTransfer[] $bundleItems
     * @param \Generated\Shared\Transfer\ItemTransfer $itemTransfer
     *
     * @return array
     */
    protected function groupBundledItems(array $bundleItems, ItemTransfer $itemTransfer, $bundleSku)
    {
        $currentBundleItems = $this->getAlreadyBundledItems($bundleItems, $bundleSku);
        $currentBundleIdentifer = $itemTransfer->getSku() . $itemTransfer->getRelatedBundleItemIdentifier();

        if (!isset($currentBundleItems[$currentBundleIdentifer])) {
            $currentBundleItems[$currentBundleIdentifer] = clone $itemTransfer;
        } else {
            $currentBundleItemTransfer = $currentBundleItems[$currentBundleIdentifer];
            $currentBundleItemTransfer->setQuantity(
                $currentBundleItemTransfer->getQuantity() + $itemTransfer->getQuantity()
            );

        }

        return $currentBundleItems;
    }

    /**
     * @param array|\Generated\Shared\Transfer\ItemTransfer[] $bundleItems
     * @param string $bundleSku
     *
     * @return \Generated\Shared\Transfer\ItemTransfer
     */
    protected function getBundleProduct(array $bundleItems, $bundleSku)
    {
        return $bundleItems[$bundleSku][static::BUNDLE_PRODUCT];
    }

    /**
     * @param array|\Generated\Shared\Transfer\ItemTransfer[] $bundleItems
     * @param string $bundleSku
     *
     * @return array
     */
    protected function getAlreadyBundledItems(array $bundleItems, $bundleSku)
    {
        return $bundleItems[$bundleSku][static::BUNDLE_ITEMS];
    }

}
