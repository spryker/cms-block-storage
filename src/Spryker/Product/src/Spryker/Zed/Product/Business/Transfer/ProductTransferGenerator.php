<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Product\Business\Transfer;

use Generated\Shared\Transfer\ProductAbstractTransfer;
use Generated\Shared\Transfer\ProductConcreteTransfer;
use Orm\Zed\Product\Persistence\SpyProduct;
use Orm\Zed\Product\Persistence\SpyProductAbstract;
use Propel\Runtime\Collection\ObjectCollection;

class ProductTransferGenerator implements ProductTransferGeneratorInterface
{

    /**
     * @param \Orm\Zed\Product\Persistence\SpyProductAbstract $productAbstractEntity
     *
     * @return \Generated\Shared\Transfer\ProductAbstractTransfer
     */
    public function convertProductAbstract(SpyProductAbstract $productAbstractEntity)
    {
        return (new ProductAbstractTransfer())
            ->fromArray($productAbstractEntity->toArray(), true);
    }

    /**
     * @param \Orm\Zed\Product\Persistence\SpyProductAbstract[]|\Propel\Runtime\Collection\ObjectCollection $productAbstractEntityCollection
     *
     * @return \Generated\Shared\Transfer\CategoryTransfer[]
     */
    public function convertProductAbstractCollection(ObjectCollection $productAbstractEntityCollection)
    {
        $transferList = [];
        foreach ($productAbstractEntityCollection as $productAbstractEntity) {
            $transferList[] = $this->convertProductAbstract($productAbstractEntity);
        }

        return $transferList;
    }

    /**
     * @param \Orm\Zed\Product\Persistence\SpyProduct $productEntity
     *
     * @return \Generated\Shared\Transfer\ProductConcreteTransfer
     */
    public function convertProduct(SpyProduct $productEntity)
    {
        return (new ProductConcreteTransfer())
            ->fromArray($productEntity->toArray(), true);
    }

    /**
     * @param \Orm\Zed\Product\Persistence\SpyProduct[]|\Propel\Runtime\Collection\ObjectCollection $productCollection
     *
     * @return \Generated\Shared\Transfer\CategoryTransfer[]
     */
    public function convertProductCollection(ObjectCollection $productCollection)
    {
        $transferList = [];
        foreach ($productCollection as $productEntity) {
            $transferList[] = $this->convertProduct($productEntity);
        }

        return $transferList;
    }

}
