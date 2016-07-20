<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductManagement\Business\Transfer;

use Generated\Shared\Transfer\LocaleTransfer;
use Generated\Shared\Transfer\LocalizedProductManagementAttributeKeyTransfer;
use Generated\Shared\Transfer\ProductManagementAttributeTransfer;
use Generated\Shared\Transfer\ProductManagementAttributeValueTransfer;
use Orm\Zed\ProductManagement\Persistence\SpyProductManagementAttribute;
use Orm\Zed\ProductManagement\Persistence\SpyProductManagementAttributeValue;
use Propel\Runtime\Collection\ObjectCollection;
use Spryker\Shared\ProductManagement\ProductManagementConstants;
use Spryker\Zed\ProductManagement\Dependency\Facade\ProductManagementToGlossaryInterface;
use Spryker\Zed\ProductManagement\Dependency\Facade\ProductManagementToLocaleInterface;

class ProductAttributeTransferGenerator implements ProductAttributeTransferGeneratorInterface
{

    /**
     * @var \Spryker\Zed\ProductManagement\Dependency\Facade\ProductManagementToLocaleInterface
     */
    protected $localeFacade;

    /**
     * @var \Spryker\Zed\ProductManagement\Dependency\Facade\ProductManagementToGlossaryInterface
     */
    protected $glossaryFacade;

    /**
     * @param \Spryker\Zed\ProductManagement\Dependency\Facade\ProductManagementToLocaleInterface $localeFacade
     * @param \Spryker\Zed\ProductManagement\Dependency\Facade\ProductManagementToGlossaryInterface $glossaryFacade
     */
    public function __construct(
        ProductManagementToLocaleInterface $localeFacade,
        ProductManagementToGlossaryInterface $glossaryFacade
    ) {
        $this->localeFacade = $localeFacade;
        $this->glossaryFacade = $glossaryFacade;
    }

    /**
     * @param \Orm\Zed\ProductManagement\Persistence\SpyProductManagementAttribute $productAttributeEntity
     *
     * @return \Generated\Shared\Transfer\ProductManagementAttributeTransfer
     */
    public function convertProductAttribute(SpyProductManagementAttribute $productAttributeEntity)
    {
        $attributeTransfer = (new ProductManagementAttributeTransfer())
            ->fromArray($productAttributeEntity->toArray(), true);

        $attributeTransfer->setKey($productAttributeEntity->getSpyProductAttributeKey()->getKey());

        $attributeTransfer = $this->setLocalizedAttributeKeys($attributeTransfer);
        $attributeTransfer = $this->setAttributeValues($attributeTransfer, $productAttributeEntity);

        return $attributeTransfer;
    }

    /**
     * @param \Orm\Zed\ProductManagement\Persistence\SpyProductManagementAttribute[]|\Propel\Runtime\Collection\ObjectCollection $productAttributeEntityCollection
     *
     * @return \Generated\Shared\Transfer\ProductManagementAttributeTransfer[]
     */
    public function convertProductAttributeCollection(ObjectCollection $productAttributeEntityCollection)
    {
        $transferList = [];
        foreach ($productAttributeEntityCollection as $productAttributeEntity) {
            $transferList[] = $this->convertProductAttribute($productAttributeEntity);
        }

        return $transferList;
    }

    /**
     * @param \Orm\Zed\ProductManagement\Persistence\SpyProductManagementAttributeValue $productAttributeValueEntity
     *
     * @return \Generated\Shared\Transfer\ProductManagementAttributeValueTransfer
     */
    public function convertProductAttributeValue(SpyProductManagementAttributeValue $productAttributeValueEntity)
    {
        $productAttributeTransfer = (new ProductManagementAttributeValueTransfer())
            ->fromArray($productAttributeValueEntity->toArray(), true);

        return $productAttributeTransfer;
    }

    /**
     * @param \Orm\Zed\ProductManagement\Persistence\SpyProductManagementAttributeValue[]|\Propel\Runtime\Collection\ObjectCollection $productAttributeValueEntityCollection
     *
     * @return \Generated\Shared\Transfer\ProductManagementAttributeValueTransfer[]
     */
    public function convertProductAttributeValueCollection(ObjectCollection $productAttributeValueEntityCollection)
    {
        $transferList = [];
        foreach ($productAttributeValueEntityCollection as $productAttributeValueEntity) {
            $transferList[] = $this->convertProductAttributeValue($productAttributeValueEntity);
        }

        return $transferList;
    }

    /**
     * @param \Generated\Shared\Transfer\ProductManagementAttributeTransfer $attributeTransfer
     * @param \Orm\Zed\ProductManagement\Persistence\SpyProductManagementAttribute $productAttributeEntity
     *
     * @return \Generated\Shared\Transfer\ProductManagementAttributeTransfer
     */
    protected function setAttributeValues(ProductManagementAttributeTransfer $attributeTransfer, SpyProductManagementAttribute $productAttributeEntity)
    {
        foreach ($productAttributeEntity->getSpyProductManagementAttributeValues() as $attributeValueEntity) {
            $attributeValueTransferData = $attributeValueEntity->toArray();
            $attributeValueTransferData[ProductManagementAttributeValueTransfer::LOCALIZED_VALUES] = $attributeValueEntity
                ->getSpyProductManagementAttributeValueTranslations()
                ->toArray();

            $attributeValueTransfer = (new ProductManagementAttributeValueTransfer())
                ->fromArray($attributeValueTransferData, true);

            $attributeTransfer->addValue($attributeValueTransfer);
        }

        return $attributeTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\ProductManagementAttributeTransfer $attributeTransfer
     *
     * @return \Generated\Shared\Transfer\ProductManagementAttributeTransfer
     */
    protected function setLocalizedAttributeKeys(ProductManagementAttributeTransfer $attributeTransfer)
    {
        $availableLocales = $this->localeFacade->getAvailableLocales();

        foreach ($availableLocales as $idLocale => $localeName) {
            $localeTransfer = new LocaleTransfer();
            $localeTransfer
                ->setIdLocale($idLocale)
                ->setLocaleName($localeName);

            $localizedAttributeKeyTransfer = new LocalizedProductManagementAttributeKeyTransfer();
            $localizedAttributeKeyTransfer
                ->setLocaleName($localeName)
                ->setKeyTranslation($this->getAttributeKeyTranslation($attributeTransfer->getKey(), $localeTransfer));

            $attributeTransfer->addLocalizedKey($localizedAttributeKeyTransfer);
        }

        return $attributeTransfer;
    }

    /**
     * @param string $attributeKey
     * @param \Generated\Shared\Transfer\LocaleTransfer $localeTransfer
     *
     * @return string
     */
    protected function getAttributeKeyTranslation($attributeKey, LocaleTransfer $localeTransfer)
    {
        $glossaryKey = ProductManagementConstants::PRODUCT_MANAGEMENT_ATTRIBUTE_GLOSSARY_PREFIX . $attributeKey;

        if ($this->glossaryFacade->hasTranslation($glossaryKey, $localeTransfer)) {
            return $this->glossaryFacade
                ->getTranslation($glossaryKey, $localeTransfer)
                ->getValue();
        }

        return null;
    }

}
