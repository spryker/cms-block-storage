<?php

namespace SprykerFeature\Zed\ProductOption\Business\Model;

use SprykerFeature\Zed\ProductOption\Business\Exception\MissingProductOptionTypeException;
use SprykerFeature\Zed\ProductOption\Business\Exception\MissingProductOptionValueException;
use SprykerFeature\Zed\ProductOption\Business\Exception\MissingProductOptionTypeUsageException;
use SprykerFeature\Zed\ProductOption\Business\Exception\MissingProductOptionValueUsageException;

interface DataImportWriterInterface
{

    /**
     * @param string $importKeyProductOptionType
     * @param array $localizedNames
     * @param string $importKeyTaxSet
     *
     * @return int
     */
    public function importProductOptionType($importKeyProductOptionType, array $localizedNames = [], $importKeyTaxSet = null);

    /**
     * @param string $importKeyProductOptionValue
     * @param string $importKeyProductOptionType
     * @param array $localizedNames
     * @param float $price
     *
     * @return int
     *
     * @throws MissingProductOptionTypeException
     */
    public function importProductOptionValue($importKeyProductOptionValue, $importKeyProductOptionType, array $localizedNames = [], $price = null);

    /**
     * @param string $sku
     * @param string $importKeyProductOptionType
     * @param bool $isOptional
     * @param int $sequence
     *
     * @return int
     *
     * @throws MissingProductOptionTypeException
     */
    public function importProductOptionTypeUsage($sku, $importKeyProductOptionType, $isOptional = false, $sequence = null);

    /**
     * @param int $idProductOptionTypeUsage
     * @param string $importKeyProductOptionValue
     * @param int $sequence
     *
     * @return int
     *
     * @throws MissingProductOptionTypeUsageException
     * @throws MissingProductOptionValueException
     */
    public function importProductOptionValueUsage($idProductOptionTypeUsage, $importKeyProductOptionValue, $sequence = null);

    /**
     * @param string $sku
     * @param string $importKeyProductOptionTypeA
     * @param string $importKeyProductOptionTypeB
     *
     * @throws MissingProductOptionTypeException
     * @throws MissingProductOptionTypeUsageException
     */
    public function importProductOptionTypeUsageExclusion($sku, $importKeyProductOptionTypeA, $importKeyProductOptionTypeB);

    /**
     * @param string $sku
     * @param int $idProductOptionValueUsageSource
     * @param string $importKeyProductOptionValueTarget
     * @param string $operator
     *
     * @throws MissingProductOptionValueUsageException
     * @throws MissingProductOptionValueException
     * @throws MissingProductOptionValueUsageException
     */
    public function importProductOptionValueUsageConstraint($sku, $idProductOptionValueUsageSource, $importKeyProductOptionValueTarget, $operator);

    /**
     * @param string $sku
     * @param array $importKeysProductOptionValues
     * @param bool $isDefault
     * @param int $sequence
     *
     * @return int
     *
     * @throws MissingProductOptionValueUsageException
     * @throws MissingProductOptionValueException
     * @throws MissingProductOptionValueUsageException
     */
    public function importPresetConfiguration($sku, array $importKeysProductOptionValues, $isDefault = false, $sequence = null);
}
