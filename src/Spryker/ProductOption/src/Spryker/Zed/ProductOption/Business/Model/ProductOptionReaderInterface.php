<?php

/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace Spryker\Zed\ProductOption\Business\Model;

use Generated\Shared\Transfer\ProductOptionTransfer;

interface ProductOptionReaderInterface
{

    /**
     * @param int $idProductOptionValueUsage
     * @param int $idLocale
     *
     * @return ProductOptionTransfer
     */
    public function getProductOption($idProductOptionValueUsage, $idLocale);

    /**
     * @param int $idProduct
     * @param int $idLocale
     *
     * @return array
     */
    public function getTypeUsagesForProductConcrete($idProduct, $idLocale);

    /**
     * @param int $idProductAttributeTypeUsage
     * @param int $idLocale
     *
     * @return array
     */
    public function getValueUsagesForTypeUsage($idProductAttributeTypeUsage, $idLocale);

    /**
     * @param int $idProductAttributeTypeUsage
     *
     * @return array
     */
    public function getTypeExclusionsForTypeUsage($idProductAttributeTypeUsage);

    /**
     * @param int $idValueUsage
     *
     * @return array
     */
    public function getValueConstraintsForValueUsage($idValueUsage);

    /**
     * @param int $idValueUsage
     * @param string $operator
     *
     * @return array
     */
    public function getValueConstraintsForValueUsageByOperator($idValueUsage, $operator);

    /**
     * @param int $idProduct
     *
     * @return array
     */
    public function getConfigPresetsForProductConcrete($idProduct);

    /**
     * @param int $idConfigPreset
     *
     * @return array
     */
    public function getValueUsagesForConfigPreset($idConfigPreset);

    /**
     * @param int $idProductAttributeTypeUsage
     *
     * @return string|null
     */
    public function getEffectiveTaxRateForTypeUsage($idProductAttributeTypeUsage);

}
