<?php
/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductOption\Business\Model;

use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Spryker\Zed\ProductOption\Dependency\Facade\ProductOptionToTaxBridgeInterface;
use Spryker\Zed\ProductOption\Persistence\ProductOptionQueryContainer;
use Spryker\Zed\ProductOption\Persistence\ProductOptionQueryContainerInterface;

class ProductOptionTaxRateCalculator implements CalculatorInterface
{

    /**
     * @var \Spryker\Zed\ProductOption\Persistence\ProductOptionQueryContainerInterface
     */
    protected $queryContainer;

    /**
     * @var \Spryker\Zed\ProductOption\Dependency\Facade\ProductOptionToTaxBridgeInterface
     */
    protected $taxFacade;

    /**
     * @param \Spryker\Zed\ProductOption\Persistence\ProductOptionQueryContainerInterface $queryContainer
     * @param \Spryker\Zed\ProductOption\Dependency\Facade\ProductOptionToTaxBridgeInterface $taxFacade
     */
    public function __construct(ProductOptionQueryContainerInterface $queryContainer, ProductOptionToTaxBridgeInterface $taxFacade)
    {
        $this->queryContainer = $queryContainer;
        $this->taxFacade = $taxFacade;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return void
     */
    public function recalculate(QuoteTransfer $quoteTransfer)
    {
        $country = $this->getShippingCountryIsoCode($quoteTransfer);
        $allIdOptionValueUsages = $this->getAllIdOptionValueUsages($quoteTransfer);

        $taxRates = $this->findTaxRatesByIdOptionValueUsageAndCountry($allIdOptionValueUsages, $country);

        $this->setItemsTaxRate($quoteTransfer, $taxRates);
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return string
     */
    protected function getShippingCountryIsoCode(QuoteTransfer $quoteTransfer)
    {
        if ($quoteTransfer->getShippingAddress() === null) {
            return $this->taxFacade->getDefaultTaxCountry();
        }

        return $quoteTransfer->getShippingAddress()->getIso2Code();
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return array
     */
    protected function getAllIdOptionValueUsages(QuoteTransfer $quoteTransfer)
    {
        $allIdOptionValueUsages = [];
        foreach ($quoteTransfer->getItems() as $item) {
            $allIdOptionValueUsages = array_merge($allIdOptionValueUsages, $this->getAllIdOptionValueUsagesPerItem($item));
        }

        return $allIdOptionValueUsages;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param array $taxRates
     *
     * @return void
     */
    protected function setItemsTaxRate(QuoteTransfer $quoteTransfer, array $taxRates)
    {
        foreach ($quoteTransfer->getItems() as $item) {
            $this->setProductOptionTaxRate($item, $taxRates);
        }
    }

    /**
     * @param array $taxRates
     * @param int $idOptionValueUsage
     *
     * @return float
     */
    protected function getEffectiveTaxRate(array $taxRates, $idOptionValueUsage)
    {
        foreach ($taxRates as $taxRate) {
            if ($taxRate[ProductOptionQueryContainer::COL_ID_PRODUCT_OPTION_VALUE_USAGE] === $idOptionValueUsage) {
                return (float)$taxRate[ProductOptionQueryContainer::COL_SUM_TAX_RATE];
            }
        }

        return $this->taxFacade->getDefaultTaxRate();
    }

    /**
     * @param \Generated\Shared\Transfer\ItemTransfer $item
     *
     * @return array
     */
    protected function getAllIdOptionValueUsagesPerItem(ItemTransfer $item)
    {
        $allIdOptionValueUsagesPerItem = [];
        foreach ($item->getProductOptions() as $productOption) {
            $allIdOptionValueUsagesPerItem[] = $productOption->getIdOptionValueUsage();
        }

        return $allIdOptionValueUsagesPerItem;
    }

    /**
     * @param \Generated\Shared\Transfer\ItemTransfer $item
     * @param array $taxRates
     *
     * @return void
     */
    protected function setProductOptionTaxRate(ItemTransfer $item, array $taxRates)
    {
        foreach ($item->getProductOptions() as $productOption) {
            $productOption->setTaxRate($this->getEffectiveTaxRate($taxRates, $productOption->getIdOptionValueUsage()));
        }
    }

    /**
     * @param int[] $allIdOptionValueUsages
     * @param string $country
     *
     * @return \Orm\Zed\Shipment\Persistence\SpyShipmentMethod[]|\Propel\Runtime\Collection\ObjectCollection
     */
    protected function findTaxRatesByIdOptionValueUsageAndCountry($allIdOptionValueUsages, $country)
    {
        return $this->queryContainer->queryTaxSetByIdProductOptionValueUsagesAndCountry($allIdOptionValueUsages, $country)
            ->find()
            ->toArray();
    }

}
