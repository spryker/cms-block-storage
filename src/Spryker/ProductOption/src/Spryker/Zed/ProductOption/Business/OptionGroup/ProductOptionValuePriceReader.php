<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductOption\Business\OptionGroup;

use Orm\Zed\ProductOption\Persistence\SpyProductOptionValue;
use Propel\Runtime\Collection\ObjectCollection;
use Spryker\Zed\ProductOption\Dependency\Facade\ProductOptionToCurrencyInterface;
use Spryker\Zed\ProductOption\Dependency\Facade\ProductOptionToStoreInterface;

class ProductOptionValuePriceReader implements ProductOptionValuePriceReaderInterface
{
    /**
     * @var \Spryker\Zed\ProductOption\Dependency\Facade\ProductOptionToCurrencyInterface
     */
    protected $currencyFacade;

    /**
     * @var \Spryker\Zed\ProductOption\Dependency\Facade\ProductOptionToStoreInterface
     */
    protected $storeFacade;

    /**
     * @param \Spryker\Zed\ProductOption\Dependency\Facade\ProductOptionToCurrencyInterface $currencyFacade
     * @param \Spryker\Zed\ProductOption\Dependency\Facade\ProductOptionToStoreInterface $storeFacade
     */
    public function __construct(ProductOptionToCurrencyInterface $currencyFacade, ProductOptionToStoreInterface $storeFacade)
    {
        $this->currencyFacade = $currencyFacade;
        $this->storeFacade = $storeFacade;
    }

    /**
     * @param \Orm\Zed\ProductOption\Persistence\SpyProductOptionValue $productOptionValueEntity
     *
     * @return int|null
     */
    public function getCurrentGrossPrice(SpyProductOptionValue $productOptionValueEntity)
    {
        $currentIdCurrency = $this->currencyFacade->getCurrent()->getIdCurrency();
        $currentIdStore = $this->storeFacade->getCurrentStore()->getIdStore();

        $priceMap = $this->getCurrencyFilteredPriceMap($productOptionValueEntity->getProductOptionValuePrices(), $currentIdCurrency);

        if (isset($priceMap[$currentIdStore])) {
            return $priceMap[$currentIdStore]->getGrossPrice();
        }

        if (isset($priceMap[null])) {
            return $priceMap[null]->getGrossPrice();
        }

        return null;
    }

    /**
     * @param \Orm\Zed\ProductOption\Persistence\SpyProductOptionValue $productOptionValueEntity
     *
     * @return int|null
     */
    public function getCurrentNetPrice(SpyProductOptionValue $productOptionValueEntity)
    {
        $currentIdCurrency = $this->currencyFacade->getCurrent()->getIdCurrency();
        $currentIdStore = $this->storeFacade->getCurrentStore()->getIdStore();

        $priceMap = $this->getCurrencyFilteredPriceMap($productOptionValueEntity->getProductOptionValuePrices(), $currentIdCurrency);

        if (isset($priceMap[$currentIdStore])) {
            return $priceMap[$currentIdStore]->getNetPrice();
        }

        if (isset($priceMap[null])) {
            return $priceMap[null]->getNetPrice();
        }

        return null;
    }

    /**
     * @param \Propel\Runtime\Collection\ObjectCollection|\Orm\Zed\ProductOption\Persistence\SpyProductOptionValuePrice[] $priceCollection
     * @param int $idCurrency
     *
     * @return \Orm\Zed\ProductOption\Persistence\SpyProductOptionValuePrice[] Keys are store ids, values are ProductOptionValuePrice entities.
     */
    protected function getCurrencyFilteredPriceMap(ObjectCollection $priceCollection, $idCurrency)
    {
        $priceMap = [];
        foreach ($priceCollection as $price) {
            if ($price->getFkCurrency() !== $idCurrency) {
                continue;
            }

            $priceMap[$price->getFkStore()] = $price;
        }

        return $priceMap;
    }
}
