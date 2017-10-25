<?php
/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductOption\Communication\Table;

use Generated\Shared\Transfer\CurrencyTransfer;
use Generated\Shared\Transfer\MoneyTransfer;
use Orm\Zed\ProductOption\Persistence\Map\SpyProductOptionGroupTableMap;
use Orm\Zed\ProductOption\Persistence\Map\SpyProductOptionValueTableMap;
use Orm\Zed\ProductOption\Persistence\SpyProductOptionGroup;
use Propel\Runtime\Collection\ObjectCollection;
use Spryker\Service\UtilText\Model\Url\Url;
use Spryker\Zed\Gui\Communication\Table\AbstractTable;
use Spryker\Zed\Gui\Communication\Table\TableConfiguration;
use Spryker\Zed\ProductOption\Dependency\Facade\ProductOptionToCurrencyInterface;
use Spryker\Zed\ProductOption\Dependency\Facade\ProductOptionToMoneyInterface;
use Spryker\Zed\ProductOption\Persistence\ProductOptionQueryContainerInterface;

class ProductOptionListTable extends AbstractTable
{
    const TABLE_COL_PRICE = 'price';
    const TABLE_COL_GROSS_PRICE = 'gross_price';
    const TABLE_COL_NET_PRICE = 'net_price';
    const TABLE_COL_SKU = 'sku';
    const TABLE_COL_NAME = 'name';
    const TABLE_COL_ACTIONS = 'Actions';

    const URL_PARAM_ID_PRODUCT_OPTION_GROUP = 'id-product-option-group';
    const URL_PARAM_ACTIVE = 'active';
    const URL_PARAM_REDIRECT_URL = 'redirect-url';

    const PRICE_LABEL = '<span class="label label-info">%s</span>';

    /**
     * @var \Spryker\Zed\ProductOption\Persistence\ProductOptionQueryContainerInterface
     */
    protected $productOptionQueryContainer;

    /**
     * @var \Spryker\Zed\ProductOption\Dependency\Facade\ProductOptionToCurrencyInterface
     */
    protected $currencyFacade;

    /**
     * @var \Spryker\Zed\ProductOption\Dependency\Facade\ProductOptionToMoneyInterface
     */
    protected $moneyFacade;

    /**
     * @var array Keys are currency ids, values are currency transfer objects in array format.
     */
    protected static $currencyCache = [];

    /**
     * @param \Spryker\Zed\ProductOption\Persistence\ProductOptionQueryContainerInterface $productOptionQueryContainer
     * @param \Spryker\Zed\ProductOption\Dependency\Facade\ProductOptionToCurrencyInterface $currencyFacade
     * @param \Spryker\Zed\ProductOption\Dependency\Facade\ProductOptionToMoneyInterface $moneyFacade
     */
    public function __construct(ProductOptionQueryContainerInterface $productOptionQueryContainer, ProductOptionToCurrencyInterface $currencyFacade, ProductOptionToMoneyInterface $moneyFacade)
    {
        $this->productOptionQueryContainer = $productOptionQueryContainer;
        $this->currencyFacade = $currencyFacade;
        $this->moneyFacade = $moneyFacade;
    }

    /**
     * @param \Spryker\Zed\Gui\Communication\Table\TableConfiguration $config
     *
     * @return \Spryker\Zed\Gui\Communication\Table\TableConfiguration
     */
    protected function configure(TableConfiguration $config)
    {
        $url = Url::generate('list-table')->build();
        $config->setUrl($url);

        $config->setHeader([
            SpyProductOptionGroupTableMap::COL_ID_PRODUCT_OPTION_GROUP => 'Option group ID',
            SpyProductOptionGroupTableMap::COL_NAME => 'Group name',
            self::TABLE_COL_SKU => 'SKU',
            self::TABLE_COL_NAME => 'Name',
            static::TABLE_COL_GROSS_PRICE => 'Gross Price',
            static::TABLE_COL_NET_PRICE => 'Net Price',
            SpyProductOptionGroupTableMap::COL_ACTIVE => 'Status',
            self::TABLE_COL_ACTIONS => 'Actions',
        ]);

        $config->setSearchable([
            SpyProductOptionValueTableMap::COL_SKU,
            SpyProductOptionValueTableMap::COL_VALUE,
            SpyProductOptionGroupTableMap::COL_ID_PRODUCT_OPTION_GROUP,
            SpyProductOptionGroupTableMap::COL_NAME,
        ]);

        $config->setSortable([
            SpyProductOptionGroupTableMap::COL_ID_PRODUCT_OPTION_GROUP,
            SpyProductOptionGroupTableMap::COL_ACTIVE,
            SpyProductOptionGroupTableMap::COL_NAME,
        ]);

        $config->setDefaultSortColumnIndex(0);
        $config->setDefaultSortDirection(TableConfiguration::SORT_DESC);

        $config->addRawColumn(self::TABLE_COL_ACTIONS);
        $config->addRawColumn(self::TABLE_COL_SKU);
        $config->addRawColumn(self::TABLE_COL_GROSS_PRICE);
        $config->addRawColumn(self::TABLE_COL_NET_PRICE);
        $config->addRawColumn(self::TABLE_COL_NAME);
        $config->addRawColumn(SpyProductOptionGroupTableMap::COL_ACTIVE);

        return $config;
    }

    /**
     * @param \Spryker\Zed\Gui\Communication\Table\TableConfiguration $config
     *
     * @return mixed
     */
    protected function prepareData(TableConfiguration $config)
    {
        $result = [];

        $productQuery = $this->productOptionQueryContainer->queryAllProductOptionGroups();

        $queryResult = $this->runQuery($productQuery, $config, true);

        /** @var \Orm\Zed\ProductOption\Persistence\SpyProductOptionGroup $productOptionGroupEntity */
        foreach ($queryResult as $productOptionGroupEntity) {
            $result[] = [
                SpyProductOptionGroupTableMap::COL_ID_PRODUCT_OPTION_GROUP => $productOptionGroupEntity->getIdProductOptionGroup(),
                SpyProductOptionGroupTableMap::COL_NAME => $productOptionGroupEntity->getName(),
                static::TABLE_COL_SKU => $this->formatSkus($productOptionGroupEntity),
                static::TABLE_COL_NAME => $this->formatNames($productOptionGroupEntity),
                static::TABLE_COL_GROSS_PRICE => $this->getFormattedGrossPrices($productOptionGroupEntity),
                static::TABLE_COL_NET_PRICE => $this->getFormattedNetPrices($productOptionGroupEntity),
                SpyProductOptionGroupTableMap::COL_ACTIVE => $this->getStatus($productOptionGroupEntity),
                static::TABLE_COL_ACTIONS => $this->getActionButtons($productOptionGroupEntity),
            ];
        }

        return $result;
    }

    /**
     * @param \Propel\Runtime\Collection\ObjectCollection|\Orm\Zed\ProductOption\Persistence\SpyProductOptionValue[] $productOptionValueCollection
     *
     * @return string[] First level keys are product option value ids,
     *                  second level keys are product option price ids,
     *                  values are formatted gross prices with symbol.
     */
    public function getGrossPriceCollection(ObjectCollection $productOptionValueCollection)
    {
        $grossPriceCollection = [];
        foreach ($productOptionValueCollection as $productOptionValueEntity) {
            foreach ($productOptionValueEntity->getProductOptionValuePrices() as $productOptionPriceEntity) {
                $grossPriceCollection[$productOptionValueEntity->getIdProductOptionValue()][$productOptionPriceEntity->getIdProductOptionValuePrice()] =
                    $this->formatPrice($productOptionPriceEntity->getNetPrice(), $productOptionPriceEntity->getFkCurrency());
            }
        }

        return $grossPriceCollection;
    }

    /**
     * @param \Propel\Runtime\Collection\ObjectCollection|\Orm\Zed\ProductOption\Persistence\SpyProductOptionValue[] $productOptionValueCollection
     *
     * @return string[] First level keys are product option value ids,
     *                  second level keys are product option price ids,
     *                  values are formatted net prices with symbol.
     */
    public function getNetPriceCollection(ObjectCollection $productOptionValueCollection)
    {
        $grossNetCollection = [];
        foreach ($productOptionValueCollection as $productOptionValueEntity) {
            foreach ($productOptionValueEntity->getProductOptionValuePrices() as $productOptionPriceEntity) {
                $grossNetCollection[$productOptionValueEntity->getIdProductOptionValue()][$productOptionPriceEntity->getIdProductOptionValuePrice()] =
                    $this->formatPrice($productOptionPriceEntity->getGrossPrice(), $productOptionPriceEntity->getFkCurrency());
            }
        }

        return $grossNetCollection;
    }

    /**
     * @param int $price
     * @param int $idCurrency
     *
     * @return string
     */
    protected function formatPrice($price, $idCurrency)
    {
        if ($price === null) {
            return '';
        }

        return $this->moneyFacade->formatWithSymbol(
            (new MoneyTransfer())
                ->setAmount($price)
                ->setCurrency($this->getCurrencyTransfer($idCurrency))
        );
    }

    /**
     * @param int $idCurrency
     *
     * @return \Generated\Shared\Transfer\CurrencyTransfer
     */
    protected function getCurrencyTransfer($idCurrency)
    {
        if (!isset(static::$currencyCache[$idCurrency])) {
            static::$currencyCache[$idCurrency] = $this->currencyFacade
                ->getByIdCurrency($idCurrency)
                ->toArray();
        }

        return (new CurrencyTransfer())->fromArray(static::$currencyCache[$idCurrency]);
    }

    /**
     * @param \Orm\Zed\ProductOption\Persistence\SpyProductOptionGroup $entity
     *
     * @return string
     */
    protected function getFormattedNetPrices(SpyProductOptionGroup $entity)
    {
        $netPriceCollection = $this->getNetPriceCollection($entity->getSpyProductOptionValues());

        $prices = '';
        foreach ($netPriceCollection as $productOptionValuePrices) {
            $prices .= $this->wrapInlineCellItem(implode(' ', $productOptionValuePrices));
        }

        return $prices;
    }

    /**
     * @param \Orm\Zed\ProductOption\Persistence\SpyProductOptionGroup $entity
     *
     * @return string
     */
    protected function getFormattedGrossPrices(SpyProductOptionGroup $entity)
    {
        $grossPriceCollection = $this->getGrossPriceCollection($entity->getSpyProductOptionValues());

        $prices = '';
        foreach ($grossPriceCollection as $productOptionValuePrices) {
            $prices .= $this->wrapInlineCellItem(implode(' ', $productOptionValuePrices));
        }

        return $prices;
    }

    /**
     * @param \Orm\Zed\ProductOption\Persistence\SpyProductOptionGroup $productOptionGroupEntity
     *
     * @return string
     */
    protected function formatSkus(SpyProductOptionGroup $productOptionGroupEntity)
    {
        $skus = '';
        foreach ($productOptionGroupEntity->getSpyProductOptionValues() as $productOptionValueEntity) {
            $skus .= $this->wrapInlineCellItem($productOptionValueEntity->getSku());
        }
        return $skus;
    }

    /**
     * @param \Orm\Zed\ProductOption\Persistence\SpyProductOptionGroup $productOptionGroupEntity
     *
     * @return string
     */
    protected function formatNames(SpyProductOptionGroup $productOptionGroupEntity)
    {
        $names = '';
        foreach ($productOptionGroupEntity->getSpyProductOptionValues() as $productOptionValueEntity) {
            $names .= $this->wrapInlineCellItem($productOptionValueEntity->getValue());
        }
        return $names;
    }

    /**
     * @param string $item
     *
     * @return string
     */
    protected function wrapInlineCellItem($item)
    {
        return '<p>' . $item . '</p>';
    }

    /**
     * @param \Orm\Zed\ProductOption\Persistence\SpyProductOptionGroup $productOptionGroupEntity
     *
     * @return string
     */
    protected function getStatus(SpyProductOptionGroup $productOptionGroupEntity)
    {
        if ($productOptionGroupEntity->getActive()) {
            return '<p class="text-success">Active</p>';
        }

        return '<p class="text-danger">Inactive</p>';
    }

    /**
     * @param \Orm\Zed\ProductOption\Persistence\SpyProductOptionGroup $productOptionGroupEntity
     *
     * @return string
     */
    protected function getActionButtons(SpyProductOptionGroup $productOptionGroupEntity)
    {
        $buttons = [];
        $buttons[] = $this->createEditButton($productOptionGroupEntity);
        $buttons[] = $this->createViewButton($productOptionGroupEntity);
        $buttons[] = $this->createDeativateButton($productOptionGroupEntity);

        return implode(' ', $buttons);
    }

    /**
     * @param \Orm\Zed\ProductOption\Persistence\SpyProductOptionGroup $productOptionGroupEntity
     *
     * @return string
     */
    protected function createViewButton(SpyProductOptionGroup $productOptionGroupEntity)
    {
        $viewProductOptionUrl = Url::generate(
            '/product-option/view/index',
            [
                self::URL_PARAM_ID_PRODUCT_OPTION_GROUP => $productOptionGroupEntity->getIdProductOptionGroup(),
            ]
        );

        return $this->generateViewButton($viewProductOptionUrl, 'View');
    }

    /**
     * @param \Orm\Zed\ProductOption\Persistence\SpyProductOptionGroup $productOptionGroupEntity
     *
     * @return string
     */
    protected function createEditButton(SpyProductOptionGroup $productOptionGroupEntity)
    {
        $editProductOptionUrl = Url::generate(
            '/product-option/edit/index',
            [
                self::URL_PARAM_ID_PRODUCT_OPTION_GROUP => $productOptionGroupEntity->getIdProductOptionGroup(),
            ]
        );

        return $this->generateEditButton($editProductOptionUrl, 'Edit');
    }

    /**
     * @param \Orm\Zed\ProductOption\Persistence\SpyProductOptionGroup $productOptionGroupEntity
     *
     * @return string
     */
    protected function createDeativateButton(SpyProductOptionGroup $productOptionGroupEntity)
    {
        $redirectUrl = Url::generate('/product-option/list/index')->build();

        $editProductOptionUrl = Url::generate(
            '/product-option/index/toggle-active',
            [
                self::URL_PARAM_ID_PRODUCT_OPTION_GROUP => $productOptionGroupEntity->getIdProductOptionGroup(),
                self::URL_PARAM_ACTIVE => $productOptionGroupEntity->getActive() ? 0 : 1,
                self::URL_PARAM_REDIRECT_URL => $redirectUrl,
            ]
        );

        return $this->generateStatusButton($editProductOptionUrl, $productOptionGroupEntity->getActive());
    }

    /**
     * @param \Spryker\Service\UtilText\Model\Url\Url $viewDiscountUrl
     * @param string $isActive
     *
     * @return string
     */
    protected function generateStatusButton(Url $viewDiscountUrl, $isActive)
    {
        if ($isActive) {
            return $this->generateRemoveButton($viewDiscountUrl, 'Deactivate');
        }

        return $this->generateViewButton($viewDiscountUrl, 'Activate');
    }
}
