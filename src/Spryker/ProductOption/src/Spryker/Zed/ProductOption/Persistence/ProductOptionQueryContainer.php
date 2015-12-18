<?php

/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace Spryker\Zed\ProductOption\Persistence;

use Spryker\Zed\Kernel\Persistence\AbstractQueryContainer;
use Orm\Zed\Product\Persistence\Base\SpyProductAbstractQuery;
use Orm\Zed\Product\Persistence\Map\SpyProductAbstractTableMap;
use Orm\Zed\ProductOption\Persistence\Map\SpyProductOptionConfigurationPresetTableMap;
use Orm\Zed\ProductOption\Persistence\Map\SpyProductOptionConfigurationPresetValueTableMap;
use Orm\Zed\ProductOption\Persistence\Map\SpyProductOptionTypeTranslationTableMap;
use Orm\Zed\ProductOption\Persistence\Map\SpyProductOptionTypeUsageExclusionTableMap;
use Orm\Zed\ProductOption\Persistence\Map\SpyProductOptionTypeUsageTableMap;
use Orm\Zed\ProductOption\Persistence\Map\SpyProductOptionValuePriceTableMap;
use Orm\Zed\ProductOption\Persistence\Map\SpyProductOptionValueTranslationTableMap;
use Orm\Zed\ProductOption\Persistence\Map\SpyProductOptionValueUsageConstraintTableMap;
use Orm\Zed\ProductOption\Persistence\Map\SpyProductOptionValueUsageTableMap;
use Orm\Zed\ProductOption\Persistence\SpyProductOptionConfigurationPresetQuery;
use Orm\Zed\ProductOption\Persistence\SpyProductOptionConfigurationPresetValueQuery;
use Orm\Zed\ProductOption\Persistence\SpyProductOptionTypeQuery;
use Orm\Zed\ProductOption\Persistence\SpyProductOptionTypeTranslationQuery;
use Orm\Zed\ProductOption\Persistence\SpyProductOptionTypeUsageExclusionQuery;
use Orm\Zed\ProductOption\Persistence\SpyProductOptionTypeUsageQuery;
use Orm\Zed\ProductOption\Persistence\SpyProductOptionValueQuery;
use Orm\Zed\ProductOption\Persistence\SpyProductOptionValueTranslationQuery;
use Orm\Zed\ProductOption\Persistence\SpyProductOptionValueUsageConstraintQuery;
use Orm\Zed\ProductOption\Persistence\SpyProductOptionValueUsageQuery;
use Orm\Zed\Tax\Persistence\Map\SpyTaxRateTableMap;
use Orm\Zed\Tax\Persistence\SpyTaxSetQuery;

class ProductOptionQueryContainer extends AbstractQueryContainer implements ProductOptionQueryContainerInterface
{

    const VALUE_USAGE_ID = 'valueUsageId';
    const ID_VALUE_USAGE = 'idValueUsage';
    const ID_TYPE_USAGE = 'idTypeUsage';
    const PRESET_ID = 'presetId';
    const IS_DEFAULT = 'isDefault';
    const IS_OPTIONAL = 'isOptional';
    const SEQUENCE = 'sequence';
    const OPERATOR = 'operator';
    const EXCLUDES = 'excludes';
    const TAX_RATE = 'taxRate';
    const LABEL = 'label';
    const PRICE = 'price';

    /**
     * @param string $importKeyProductOptionType
     *
     * @return SpyProductOptionTypeQuery
     */
    public function queryProductOptionTypeByImportKey($importKeyProductOptionType)
    {
        return SpyProductOptionTypeQuery::create()
            ->filterByImportKey($importKeyProductOptionType);
    }

    /**
     * @param int $fkProductOptionType
     * @param int $fkLocale
     *
     * @return SpyProductOptionTypeTranslationQuery
     */
    public function queryProductOptionTypeTranslationByFks($fkProductOptionType, $fkLocale)
    {
        return SpyProductOptionTypeTranslationQuery::create()
            ->filterByFkProductOptionType($fkProductOptionType)
            ->filterByFkLocale($fkLocale);
    }

    /**
     * @param string $idProductOptionValue
     *
     * @return SpyProductOptionValueQuery
     */
    public function queryOptionValueById($idProductOptionValue)
    {
        return SpyProductOptionTypeUsageQuery::create()
            ->filterByIdProductOptionTypeUsage($idProductOptionValue);
    }

    /**
     * @param string $importKeyProductOptionValue
     * @param int $fkProductOptionType
     *
     * @return SpyProductOptionValueQuery
     */
    public function queryProductOptionValueByImportKeyAndFkProductOptionType($importKeyProductOptionValue, $fkProductOptionType)
    {
        return SpyProductOptionValueQuery::create()
            ->filterByImportKey($importKeyProductOptionValue)
            ->filterByFkProductOptionType($fkProductOptionType);
    }

    /**
     * @param string $importKeyProductOptionValue
     *
     * @return SpyProductOptionValueQuery
     */
    public function queryProductOptionValueByImportKey($importKeyProductOptionValue)
    {
        return SpyProductOptionValueQuery::create()
            ->filterByImportKey($importKeyProductOptionValue);
    }

    /**
     * @param int $fkProductOptionValue
     * @param int $fkLocale
     *
     * @return SpyProductOptionValueTranslationQuery
     */
    public function queryProductOptionValueTranslationByFks($fkProductOptionValue, $fkLocale)
    {
        return SpyProductOptionValueTranslationQuery::create()
            ->filterByFkProductOptionValue($fkProductOptionValue)
            ->filterByFkLocale($fkLocale);
    }

    /**
     * @param int $idProductOptionTypeUsage
     *
     * @return SpyProductOptionTypeUsageQuery
     */
    public function queryProductOptionTypeUsageById($idProductOptionTypeUsage)
    {
        return SpyProductOptionTypeUsageQuery::create()
            ->filterByIdProductOptionTypeUsage($idProductOptionTypeUsage);
    }

    /**
     * @param int $fkProduct
     * @param int $fkProductOptionType
     *
     * @return SpyProductOptionTypeUsageQuery
     */
    public function queryProductOptionTypeUsageByFKs($fkProduct, $fkProductOptionType)
    {
        return SpyProductOptionTypeUsageQuery::create()
            ->filterByFkProduct($fkProduct)
            ->filterByFkProductOptionType($fkProductOptionType);
    }

    /**
     * @param int $idProductOptionValueUsage
     *
     * @return SpyProductOptionValueUsageQuery
     */
    public function queryProductOptionValueUsageById($idProductOptionValueUsage)
    {
        return SpyProductOptionValueUsageQuery::create()
            ->filterByIdProductOptionValueUsage($idProductOptionValueUsage);
    }

    /**
     * @param int $fkProductOptionTypeUsage
     * @param int $fkProductOptionValue
     *
     * @return SpyProductOptionTypeUsageQuery
     */
    public function queryProductOptionValueUsageByFKs($fkProductOptionTypeUsage, $fkProductOptionValue)
    {
        return SpyProductOptionValueUsageQuery::create()
            ->filterByFkProductOptionTypeUsage($fkProductOptionTypeUsage)
            ->filterByFkProductOptionValue($fkProductOptionValue);
    }

    /**
     * @param int $fkProductOptionTypeUsage
     * @param int $fkProductOptionType
     *
     * @return SpyProductOptionTypeUsageQuery
     */
    public function queryProductOptionValueUsageIdByFKs($fkProductOptionTypeUsage, $fkProductOptionType)
    {
        return SpyProductOptionValueUsageQuery::create()
            ->filterByFkProductOptionTypeUsage($fkProductOptionTypeUsage)
            ->filterByFkProductOptionValue($fkProductOptionType);
    }

    /**
     * @param int $fkProductOptionTypeUsageA
     * @param int $fkProductOptionTypeUsageB
     *
     * @return SpyProductOptionTypeUsageExclusionQuery
     */
    public function queryProductOptionTypeUsageExclusionByFks($fkProductOptionTypeUsageA, $fkProductOptionTypeUsageB)
    {
        return SpyProductOptionTypeUsageExclusionQuery::create()
            ->filterByFkProductOptionTypeUsageA([$fkProductOptionTypeUsageA, $fkProductOptionTypeUsageB])
            ->filterByFkProductOptionTypeUsageB([$fkProductOptionTypeUsageA, $fkProductOptionTypeUsageB]);
    }

    /**
     * @param int $fkProductOptionValueUsageA
     * @param int $fkProductOptionValueUsageB
     *
     * @return SpyProductOptionValueUsageConstraintQuery
     */
    public function queryProductOptionValueUsageConstraintsByFks($fkProductOptionValueUsageA, $fkProductOptionValueUsageB)
    {
        return SpyProductOptionValueUsageConstraintQuery::create()
            ->filterByFkProductOptionValueUsageA([$fkProductOptionValueUsageA, $fkProductOptionValueUsageB])
            ->filterByFkProductOptionValueUsageB([$fkProductOptionValueUsageA, $fkProductOptionValueUsageB]);
    }

    /**
     * @param int $idProductOptionType
     *
     * @return SpyProductAbstractQuery
     */
    public function queryAssociatedAbstractProductIdsForProductOptionType($idProductOptionType)
    {
        return SpyProductAbstractQuery::create()
            ->useSpyProductQuery()
                ->useSpyProductOptionTypeUsageQuery()
                    ->useSpyProductOptionTypeQuery()
                        ->filterByIdProductOptionType($idProductOptionType)
                    ->endUse()
                ->endUse()
            ->endUse()
            ->groupByIdProductAbstract()
            ->select([SpyProductAbstractTableMap::COL_ID_PRODUCT_ABSTRACT]);
    }

    /**
     * @param int $idProductOptionValue
     *
     * @return SpyProductAbstractQuery
     */
    public function queryAssociatedAbstractProductIdsForProductOptionValue($idProductOptionValue)
    {
        return SpyProductAbstractQuery::create()
            ->useSpyProductQuery()
                ->useSpyProductOptionTypeUsageQuery()
                    ->useSpyProductOptionValueUsageQuery()
                        ->useSpyProductOptionValueQuery()
                            ->filterByIdProductOptionValue($idProductOptionValue)
                        ->endUse()
                    ->endUse()
                ->endUse()
            ->endUse()
            ->groupByIdProductAbstract()
            ->select([SpyProductAbstractTableMap::COL_ID_PRODUCT_ABSTRACT]);
    }

    /**
     * @param int $idProductOptionTypeUsage
     *
     * @return SpyProductAbstractQuery
     */
    public function queryAbstractProductIdForProductOptionTypeUsage($idProductOptionTypeUsage)
    {
        return SpyProductAbstractQuery::create()
            ->useSpyProductQuery()
                ->useSpyProductOptionTypeUsageQuery()
                    ->filterByIdProductOptionTypeUsage($idProductOptionTypeUsage)
                ->endUse()
            ->endUse()
            ->groupByIdProductAbstract();
    }

    /**
     * @param int $idProduct
     *
     * @return SpyProductOptionConfigurationPresetQuery
     */
    public function queryPresetConfigurationsForConcreteProduct($idProduct)
    {
        return SpyProductOptionConfigurationPresetQuery::create()
            ->filterByFkProduct($idProduct)
            ->orderBySequence();
    }

    /**
     * @param int $idProductOptionValueUsage
     * @param int $idLocale
     *
     * @return SpyProductOptionValueUsageQuery
     */
    public function queryProductOptionValueUsageWithAssociatedAttributes($idProductOptionValueUsage, $idLocale)
    {
        return SpyProductOptionValueUsageQuery::create()
            ->useSpyProductOptionValueQuery()
                ->useSpyProductOptionValuePriceQuery()
                ->endUse()
                ->useSpyProductOptionTypeQuery()
                    ->useSpyProductOptionTypeTranslationQuery()
                        ->filterByFkLocale($idLocale)
                    ->endUse()
                ->endUse()
                ->useSpyProductOptionValueTranslationQuery()
                    ->filterByFkLocale($idLocale)
                ->endUse()
            ->endUse()
            ->filterByIdProductOptionValueUsage($idProductOptionValueUsage);
    }

    /**
     * @param int $idProductOptionValueUsage
     *
     * @return SpyTaxSetQuery
     */
    public function queryTaxSetForProductOptionValueUsage($idProductOptionValueUsage)
    {
        return SpyTaxSetQuery::create()
            ->useSpyProductOptionTypeQuery()
                ->useSpyProductOptionValueQuery()
                    ->useSpyProductOptionValueUsageQuery()
                        ->filterByIdProductOptionValueUsage($idProductOptionValueUsage)
                    ->endUse()
                ->endUse()
            ->endUse();
    }

    /**
     * @param int $idProduct
     * @param int $idLocale
     *
     * @return array
     */
    public function queryTypeUsagesForConcreteProduct($idProduct, $idLocale)
    {
        $query = SpyProductOptionTypeUsageQuery::create()
            ->withColumn(SpyProductOptionTypeUsageTableMap::COL_ID_PRODUCT_OPTION_TYPE_USAGE, self::ID_TYPE_USAGE)
            ->withColumn(SpyProductOptionTypeUsageTableMap::COL_IS_OPTIONAL, self::IS_OPTIONAL)
            ->withColumn(SpyProductOptionTypeTranslationTableMap::COL_NAME, self::LABEL)
            ->useSpyProductOptionTypeQuery()
                ->useSpyProductOptionTypeTranslationQuery()
                    ->useSpyLocaleQuery()
                        ->filterByIdLocale($idLocale)
                    ->endUse()
                ->endUse()
            ->endUse()
            ->filterByFkProduct($idProduct)
            ->orderBySequence()
            ->select([self::ID_TYPE_USAGE, self::IS_OPTIONAL, self::LABEL])
            ->find();

        $result = $query->toArray();

        return $result;
    }

    /**
     * @param int $idProductAttributeTypeUsage
     * @param int $idLocale
     *
     * @return array
     */
    public function queryValueUsagesForTypeUsage($idProductAttributeTypeUsage, $idLocale)
    {
        $query = SpyProductOptionValueUsageQuery::create()
            ->withColumn(SpyProductOptionValueUsageTableMap::COL_ID_PRODUCT_OPTION_VALUE_USAGE, self::ID_VALUE_USAGE)
            ->withColumn(SpyProductOptionTypeUsageTableMap::COL_SEQUENCE, self::SEQUENCE)
            ->withColumn(SpyProductOptionValueTranslationTableMap::COL_NAME, self::LABEL)
            ->withColumn(SpyProductOptionValuePriceTableMap::COL_PRICE, self::PRICE)
            ->useSpyProductOptionValueQuery()
                ->leftJoinSpyProductOptionValuePrice()
                ->useSpyProductOptionValueTranslationQuery()
                    ->useSpyLocaleQuery()
                        ->filterByIdLocale($idLocale)
                    ->endUse()
                ->endUse()
            ->endUse()
            ->useSpyProductOptionTypeUsageQuery()
                ->filterByIdProductOptionTypeUsage($idProductAttributeTypeUsage)
            ->endUse()
            ->orderByIdProductOptionValueUsage()
            ->select([self::ID_VALUE_USAGE, self::SEQUENCE, self::LABEL, self::PRICE])
            ->find();

        $result = $query->toArray();

        return $result;
    }

    /**
     * @param int $idProductAttributeTypeUsage
     *
     * @return array
     */
    public function queryTypeExclusionsForTypeUsage($idProductAttributeTypeUsage)
    {
        $queryA = SpyProductOptionTypeUsageExclusionQuery::create()
            ->withColumn(SpyProductOptionTypeUsageExclusionTableMap::COL_FK_PRODUCT_OPTION_TYPE_USAGE_B, self::EXCLUDES)
            ->filterByFkProductOptionTypeUsageA($idProductAttributeTypeUsage)
            ->select([self::EXCLUDES])
            ->find();

        $queryB = SpyProductOptionTypeUsageExclusionQuery::create()
            ->withColumn(SpyProductOptionTypeUsageExclusionTableMap::COL_FK_PRODUCT_OPTION_TYPE_USAGE_A, self::EXCLUDES)
            ->filterByFkProductOptionTypeUsageB($idProductAttributeTypeUsage)
            ->select([self::EXCLUDES])
            ->find();

        $result = array_merge($queryA->toArray(), $queryB->toArray());

        return $result;
    }

    /**
     * @param int $idValueUsage
     *
     * @return array
     */
    public function queryValueConstraintsForValueUsage($idValueUsage)
    {
        $queryA = SpyProductOptionValueUsageConstraintQuery::create()
            ->withColumn(SpyProductOptionValueUsageConstraintTableMap::COL_FK_PRODUCT_OPTION_VALUE_USAGE_B, self::VALUE_USAGE_ID)
            ->filterByFkProductOptionValueUsageA($idValueUsage)
            ->orderByOperator()
            ->select([self::OPERATOR])
            ->find();

        $queryB = SpyProductOptionValueUsageConstraintQuery::create()
            ->withColumn(SpyProductOptionValueUsageConstraintTableMap::COL_FK_PRODUCT_OPTION_VALUE_USAGE_A, self::VALUE_USAGE_ID)
            ->filterByFkProductOptionValueUsageB($idValueUsage)
            ->orderByOperator()
            ->select([self::OPERATOR])
            ->find();

        $result = array_merge($queryA->toArray(), $queryB->toArray());

        return $result;
    }

    /**
     * @param int $idValueUsage
     * @param string $operator
     *
     * @return array
     */
    public function queryValueConstraintsForValueUsageByOperator($idValueUsage, $operator)
    {
        $queryA = SpyProductOptionValueUsageConstraintQuery::create()
            ->withColumn(SpyProductOptionValueUsageConstraintTableMap::COL_FK_PRODUCT_OPTION_VALUE_USAGE_B, self::VALUE_USAGE_ID)
            ->filterByFkProductOptionValueUsageA($idValueUsage)
            ->filterByOperator($operator)
            ->select([self::OPERATOR])
            ->find();

        $queryB = SpyProductOptionValueUsageConstraintQuery::create()
            ->withColumn(SpyProductOptionValueUsageConstraintTableMap::COL_FK_PRODUCT_OPTION_VALUE_USAGE_A, self::VALUE_USAGE_ID)
            ->filterByFkProductOptionValueUsageB($idValueUsage)
            ->filterByOperator($operator)
            ->select([self::OPERATOR])
            ->find();

        $mergedArray = array_merge($queryA->toArray(), $queryB->toArray());

        $result = [];
        if (!empty($mergedArray)) {
            foreach ($mergedArray as $value) {
                $result[] = $value[self::VALUE_USAGE_ID];
            }
        }

        return $result;
    }

    /**
     * @param int $idProduct
     *
     * @return array
     */
    public function queryConfigPresetsForConcreteProduct($idProduct)
    {
        $query = SpyProductOptionConfigurationPresetQuery::create()
            ->withColumn(SpyProductOptionConfigurationPresetTableMap::COL_IS_DEFAULT, self::IS_DEFAULT)
            ->withColumn(SpyProductOptionConfigurationPresetTableMap::COL_ID_PRODUCT_OPTION_CONFIGURATION_PRESET, self::PRESET_ID)
            ->filterByFkProduct($idProduct)
            ->orderBySequence()
            ->select([self::PRESET_ID, self::IS_DEFAULT])
            ->find();

        $result = $query->toArray();

        return $result;
    }

    /**
     * @param int $idConfigPreset
     *
     * @return array
     */
    public function queryValueUsagesForConfigPreset($idConfigPreset)
    {
        $query = SpyProductOptionConfigurationPresetValueQuery::create()
            ->withColumn(SpyProductOptionConfigurationPresetValueTableMap::COL_FK_PRODUCT_OPTION_VALUE_USAGE, self::VALUE_USAGE_ID)
            ->filterByFkProductOptionConfigurationPreset($idConfigPreset)
            ->select([self::VALUE_USAGE_ID])
            ->find();

        $result = $query->toArray();

        return $result;
    }

    /**
     * @param int $idProduct
     *
     * @return SpyProductOptionTypeUsageQuery
     */
    public function queryProductOptionTypeUsageByIdProduct($idProduct)
    {
        $query = SpyProductOptionTypeUsageQuery::create();
        $query->filterByFkProduct($idProduct)
            ->setDistinct();

        return $query;
    }

    /**
     * @param int $idProductAttributeTypeUsage
     *
     * @return string|null
     */
    public function queryEffectiveTaxRateForTypeUsage($idProductAttributeTypeUsage)
    {
        $query = SpyProductOptionTypeUsageQuery::create()
            ->withColumn('SUM(' . SpyTaxRateTableMap::COL_RATE . ')', self::TAX_RATE)
            ->useSpyProductOptionTypeQuery()
                ->useSpyTaxSetQuery()
                    ->useSpyTaxSetTaxQuery()
                        ->useSpyTaxRateQuery()
                        ->endUse()
                    ->endUse()
                ->endUse()
            ->endUse()
            ->filterByIdProductOptionTypeUsage($idProductAttributeTypeUsage)
            ->select([self::TAX_RATE])
            ->find();

        $result = $query->getFirst();

        return $result;
    }

}
