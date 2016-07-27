<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductManagement\Business\Transfer;

use Generated\Shared\Transfer\ProductAbstractTransfer;
use Generated\Shared\Transfer\ZedProductConcreteTransfer;
use Orm\Zed\Product\Persistence\SpyProduct;
use Orm\Zed\Product\Persistence\SpyProductAbstract;
use Propel\Runtime\Collection\ObjectCollection;
use Spryker\Shared\Library\Json;

class ProductTransferGenerator implements ProductTransferGeneratorInterface
{

    /**
     * @param \Orm\Zed\Product\Persistence\SpyProductAbstract $productAbstractEntity
     *
     * @return \Generated\Shared\Transfer\ProductAbstractTransfer
     */
    public function convertProductAbstract(SpyProductAbstract $productAbstractEntity)
    {
        $productAbstractTransfer = (new ProductAbstractTransfer())
            ->fromArray($productAbstractEntity->toArray(), true);

        $attributes = $this->decodeAttributes($productAbstractEntity->getAttributes());
        $productAbstractTransfer->setAttributes($attributes);
        $productAbstractTransfer->setTaxSetId($productAbstractEntity->getFkTaxSet());

        return $productAbstractTransfer;
    }

    /**
     * @param \Orm\Zed\Product\Persistence\SpyProductAbstract[]|\Propel\Runtime\Collection\ObjectCollection $productAbstractEntityCollection
     *
     * @return \Generated\Shared\Transfer\ProductAbstractTransfer[]
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
     * @return \Generated\Shared\Transfer\ZedProductConcreteTransfer
     */
    public function convertProduct(SpyProduct $productEntity)
    {
        $productTransfer = (new ZedProductConcreteTransfer())
            ->fromArray($productEntity->toArray(), true);

        $attributes = $this->decodeAttributes($productEntity->getAttributes());
        $productTransfer->setAttributes($attributes);
        $productTransfer->setIdProductConcrete($productEntity->getIdProduct());

        if ($productEntity->getSpyProductAbstract()) {
            $abstractTransfer = $this->convertProductAbstract($productEntity->getSpyProductAbstract());
            $productTransfer->setAbstractSku($abstractTransfer->getSku());
            $productTransfer->setFkProductAbstract($abstractTransfer->getIdProductAbstract());
        }

        return $productTransfer;
    }

    /**
     * @param \Orm\Zed\Product\Persistence\SpyProduct[]|\Propel\Runtime\Collection\ObjectCollection $productCollection
     *
     * @return \Generated\Shared\Transfer\ZedProductConcreteTransfer[]
     */
    public function convertProductCollection(ObjectCollection $productCollection)
    {
        $transferList = [];
        foreach ($productCollection as $productEntity) {
            $transferList[] = $this->convertProduct($productEntity);
        }

        return $transferList;
    }

    /**
     * @param string $json
     *
     * @return array
     */
    protected function decodeAttributes($json)
    {
        return Json::decode($json, true);
    }

}
