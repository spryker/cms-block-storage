<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductManagement\Communication\Transfer;

use ArrayObject;
use Exception;
use Generated\Shared\Transfer\LocaleTransfer;
use Generated\Shared\Transfer\LocalizedAttributesTransfer;
use Generated\Shared\Transfer\ProductAbstractTransfer;
use Generated\Shared\Transfer\ProductImageSetTransfer;
use Generated\Shared\Transfer\ProductImageTransfer;
use Generated\Shared\Transfer\ProductManagementAttributeTransfer;
use Generated\Shared\Transfer\StockProductTransfer;
use Generated\Shared\Transfer\ZedProductConcreteTransfer;
use Generated\Shared\Transfer\ZedProductPriceTransfer;
use Propel\Runtime\ActiveQuery\Criteria;
use Spryker\Shared\ProductManagement\ProductManagementConstants;
use Spryker\Zed\ProductManagement\Communication\Form\DataProvider\AbstractProductFormDataProvider;
use Spryker\Zed\ProductManagement\Communication\Form\DataProvider\LocaleProvider;
use Spryker\Zed\ProductManagement\Communication\Form\ProductConcreteFormEdit;
use Spryker\Zed\ProductManagement\Communication\Form\ProductFormAdd;
use Spryker\Zed\ProductManagement\Communication\Form\Product\AttributeVariantForm;
use Spryker\Zed\ProductManagement\Communication\Form\Product\Concrete\PriceForm as ConcretePriceForm;
use Spryker\Zed\ProductManagement\Communication\Form\Product\Concrete\StockForm;
use Spryker\Zed\ProductManagement\Communication\Form\Product\GeneralForm;
use Spryker\Zed\ProductManagement\Communication\Form\Product\ImageCollectionForm;
use Spryker\Zed\ProductManagement\Communication\Form\Product\ImageSetForm;
use Spryker\Zed\ProductManagement\Communication\Form\Product\PriceForm;
use Spryker\Zed\ProductManagement\Communication\Form\Product\SeoForm;
use Spryker\Zed\ProductManagement\Dependency\Facade\ProductManagementToLocaleInterface;
use Spryker\Zed\ProductManagement\Persistence\ProductManagementQueryContainerInterface;
use Symfony\Component\Form\FormInterface;

class ProductFormTransferGenerator implements ProductFormTransferGeneratorInterface
{

    /**
     * @var \Spryker\Zed\ProductManagement\Persistence\ProductManagementQueryContainerInterface
     */
    protected $productManagementQueryContainer;

    /**
     * @var \Spryker\Zed\ProductManagement\Communication\Form\DataProvider\LocaleProvider
     */
    protected $localeProvider;

    /**
     * @var \Spryker\Zed\ProductManagement\Dependency\Facade\ProductManagementToLocaleInterface
     */
    protected $localeFacade;

    /**
     * @param \Spryker\Zed\ProductManagement\Persistence\ProductManagementQueryContainerInterface $productManagementQueryContainer
     * @param \Spryker\Zed\ProductManagement\Dependency\Facade\ProductManagementToLocaleInterface $localeFacade
     * @param \Spryker\Zed\ProductManagement\Communication\Form\DataProvider\LocaleProvider $localeProvider
     */
    public function __construct(
        ProductManagementQueryContainerInterface $productManagementQueryContainer,
        ProductManagementToLocaleInterface $localeFacade,
        LocaleProvider $localeProvider
    ) {
        $this->productManagementQueryContainer = $productManagementQueryContainer;
        $this->localeFacade = $localeFacade;
        $this->localeProvider = $localeProvider;
    }

    /**
     * @param \Symfony\Component\Form\FormInterface $form
     *
     * @return \Generated\Shared\Transfer\ProductAbstractTransfer
     */
    public function buildProductAbstractTransfer(FormInterface $form)
    {
        $formData = $form->getData();
        $attributeValues = $this->generateAbstractAttributeArrayFromData($formData);
        $localeCollection = $this->localeProvider->getLocaleCollection();

        $productAbstractTransfer = $this->createProductAbstractTransfer(
            $formData,
            $attributeValues[ProductManagementConstants::PRODUCT_MANAGEMENT_DEFAULT_LOCALE]
        );

        $localizedData = $this->generateLocalizedData($localeCollection, $formData);

        foreach ($localizedData as $localeCode => $data) {
            $formName = ProductFormAdd::getGeneralFormName($localeCode);
            $localeTransfer = $this->localeFacade->getLocale($localeCode);

            $localizedAttributesTransfer = $this->createLocalizedAttributesTransfer(
                $form->get($formName),
                $attributeValues[$localeCode],
                $localeTransfer
            );

            $productAbstractTransfer->addLocalizedAttributes($localizedAttributesTransfer);
        }

        $priceTransfer = $this->buildProductAbstractPriceTransfer($form);
        $productAbstractTransfer->setPrice($priceTransfer);

        $imageSetCollection = $this->buildProductImageSetCollection($form);
        $productAbstractTransfer->setImageSets(
            new ArrayObject($imageSetCollection)
        );

        return $productAbstractTransfer;
    }

    /**
     * @param array $localeCollection
     * @param array $formData
     *
     * @return array
     */
    protected function generateLocalizedData(array $localeCollection, array $formData)
    {
        $localizedData = [];
        foreach ($localeCollection as $code) {
            $formName = ProductFormAdd::getGeneralFormName($code);
            $localizedData[$code] = $formData[$formName];
        }

        foreach ($localeCollection as $code) {
            $formName = ProductFormAdd::getSeoFormName($code);
            $localizedData[$code] = array_merge($localizedData[$code], $formData[$formName]);
        }

        return $localizedData;
    }

    /**
     * @param \Generated\Shared\Transfer\ProductAbstractTransfer $productAbstractTransfer
     * @param \Symfony\Component\Form\FormInterface $form
     *
     * @return \Generated\Shared\Transfer\ZedProductConcreteTransfer
     */
    public function buildProductConcreteTransfer(ProductAbstractTransfer $productAbstractTransfer, FormInterface $form, $idProduct)
    {
        $sku = $form->get(ProductConcreteFormEdit::FIELD_SKU)->getData();

        $productConcreteTransfer = new ZedProductConcreteTransfer();
        $productConcreteTransfer->setIdProductConcrete($idProduct);
        $productConcreteTransfer->setAttributes([]);
        $productConcreteTransfer->setSku($sku);
        $productConcreteTransfer->setIsActive(false);
        $productConcreteTransfer->setAbstractSku($productAbstractTransfer->getSku());
        $productConcreteTransfer->setFkProductAbstract($productAbstractTransfer->getIdProductAbstract());

        $localeCollection = $this->localeProvider->getLocaleCollection();
        foreach ($localeCollection as $localeCode) {
            $formName = ProductFormAdd::getGeneralFormName($localeCode);
            $localeTransfer = $this->localeFacade->getLocale($localeCode);

            $localizedAttributesTransfer = $this->createLocalizedAttributesTransfer(
                $form->get($formName),
                [],
                $localeTransfer
            );

            $productConcreteTransfer->addLocalizedAttributes($localizedAttributesTransfer);
        }

        $priceTransfer = $this->buildProductConcretePriceTransfer($form, $productConcreteTransfer->getIdProductConcrete());
        $productConcreteTransfer->setPrice($priceTransfer);

        $stockCollection = $this->buildProductStockCollectionTransfer($form);
        $productConcreteTransfer->setStock(new ArrayObject($stockCollection));

        $imageSetCollection = $this->buildProductImageSetCollection($form);
        $productConcreteTransfer->setImageSets(
            new ArrayObject($imageSetCollection)
        );

        return $productConcreteTransfer;
    }

    /**
     * @param array $data
     *
     * @return \Generated\Shared\Transfer\ProductAbstractTransfer
     */
    protected function createProductAbstractTransfer(array $data, array $attributes)
    {
        $attributes = array_filter($attributes);

        $productAbstractTransfer = (new ProductAbstractTransfer())
            ->setIdProductAbstract($data[ProductFormAdd::FIELD_ID_PRODUCT_ABSTRACT])
            ->setSku(
                AbstractProductFormDataProvider::slugify($data[ProductFormAdd::FIELD_SKU])
            )
            ->setAttributes($attributes)
            ->setTaxSetId($data[ProductFormAdd::FORM_PRICE_AND_TAX][PriceForm::FIELD_TAX_RATE]);

        return $productAbstractTransfer;
    }

    /**
     * @param \Symfony\Component\Form\FormInterface $form
     * @param array $abstractLocalizedAttributes
     * @param \Generated\Shared\Transfer\LocaleTransfer $localeTransfer
     *
     * @return \Generated\Shared\Transfer\LocalizedAttributesTransfer
     */
    protected function createLocalizedAttributesTransfer(FormInterface $form, array $abstractLocalizedAttributes, LocaleTransfer $localeTransfer)
    {
        $abstractLocalizedAttributes = array_filter($abstractLocalizedAttributes);
        $localizedAttributesTransfer = new LocalizedAttributesTransfer();
        $localizedAttributesTransfer->setLocale($localeTransfer);
        $localizedAttributesTransfer->setName($form->get(GeneralForm::FIELD_NAME)->getData());
        $localizedAttributesTransfer->setDescription($form->get(GeneralForm::FIELD_DESCRIPTION)->getData());
        $localizedAttributesTransfer->setAttributes($abstractLocalizedAttributes);

        if ($form->has(SeoForm::FIELD_META_TITLE)) {
            $localizedAttributesTransfer->setMetaTitle($form->get(SeoForm::FIELD_META_TITLE)->getData());
        }

        if ($form->has(SeoForm::FIELD_META_KEYWORDS)) {
            $localizedAttributesTransfer->setMetaKeywords($form->get(SeoForm::FIELD_META_KEYWORDS)->getData());
        }

        if ($form->has(SeoForm::FIELD_META_DESCRIPTION)) {
            $localizedAttributesTransfer->setMetaDescription($form->get(SeoForm::FIELD_META_DESCRIPTION)->getData());
        }

        return $localizedAttributesTransfer;
    }

    /**
     * @param array $data
     *
     * @return array
     */
    public function generateAbstractAttributeArrayFromData(array $data)
    {
        $attributes = [];
        $localeCollection = $this->localeProvider->getLocaleCollection(true);

        foreach ($localeCollection as $code) {
            $formName = ProductFormAdd::getAbstractAttributeFormName($code);
            foreach ($data[$formName] as $type => $values) {
                $attributes[$code][$type] = $values['value'];
            }
        }

        return $attributes;
    }

    /**
     * @param array $data
     * @param \Generated\Shared\Transfer\ProductManagementAttributeTransfer[] $attributeTransferCollection
     *
     * @return array
     */
    public function generateVariantAttributeArrayFromData(array $data, array $attributeTransferCollection)
    {
        $result = [];
        foreach ($data[ProductFormAdd::FORM_ATTRIBUTE_VARIANT] as $type => $values) {
            $attributeValues = $this->getVariantValues($values, $attributeTransferCollection[$type]);
            if ($attributeValues) {
                $result[$type] = $attributeValues;
            }
        }

        return $result;
    }

    /**
     * @param array $variantData
     * @param \Generated\Shared\Transfer\ProductManagementAttributeTransfer $attributeTransfer
     *
     * @throws \Exception
     *
     * @return array|null
     */
    protected function getVariantValues(array $variantData, ProductManagementAttributeTransfer $attributeTransfer)
    {
        $hasValue = $variantData[AttributeVariantForm::FIELD_NAME];
        $values = (array)$variantData[AttributeVariantForm::FIELD_VALUE];

        if (!$hasValue) {
            return null;
        }

        if (empty($hasValue)) {
            return null;
        }

        return $values;
    }

    /**
     * @param \Symfony\Component\Form\FormInterface $form
     *
     * @return \Generated\Shared\Transfer\ZedProductPriceTransfer
     */
    public function buildProductAbstractPriceTransfer(FormInterface $form)
    {
        $price = $form->get(ProductFormAdd::FORM_PRICE_AND_TAX)->get(PriceForm::FIELD_PRICE)->getData();
        $idProductAbstract = $form->get(ProductFormAdd::FIELD_ID_PRODUCT_ABSTRACT)->getData();

        $priceTransfer = (new ZedProductPriceTransfer())
            ->setIdProduct($idProductAbstract)
            ->setPrice($price);

        return $priceTransfer;
    }

    /**
     * @param \Symfony\Component\Form\FormInterface $form
     *
     * @return \Generated\Shared\Transfer\ProductImageSetTransfer[]
     */
    public function buildProductImageSetCollection(FormInterface $form)
    {
        $transferCollection = [];
        $localeCollection = $this->localeProvider->getLocaleCollection(true);

        foreach ($localeCollection as $localeCode) {
            $formName = ProductFormAdd::getImagesFormName($localeCode);

            $imageSetCollection = $form->get($formName);
            foreach ($imageSetCollection as $imageSet) {
                $imageSetTransfer = (new ProductImageSetTransfer())
                    ->fromArray($imageSet->getData(), true);

                $productImages = $this->buildProductImageCollection(
                    $imageSet->get(ImageSetForm::PRODUCT_IMAGES)->getData()
                );
                $object = new ArrayObject($productImages);
                $imageSetTransfer->setProductImages($object);

                $transferCollection[] = $imageSetTransfer;
            }
        }

        return $transferCollection;
    }

    /**
     * @param array $data
     *
     * @return array
     */
    public function buildProductImageCollection(array $data)
    {
        $result = [];
        foreach ($data as $imageData) {
            $imageTransfer = new ProductImageTransfer();
            $imageData[ImageCollectionForm::FIELD_SORT_ORDER] = (int)$imageData[ImageCollectionForm::FIELD_SORT_ORDER];
            $imageTransfer->fromArray($imageData, true);

            $result[] = $imageTransfer;
        }

        return $result;
    }

    /**
     * @param \Symfony\Component\Form\FormInterface $form
     *
     * @return \Generated\Shared\Transfer\ZedProductPriceTransfer
     */
    public function buildProductConcretePriceTransfer(FormInterface $form, $idProduct)
    {
        $price = $form->get(ProductFormAdd::FORM_PRICE_AND_TAX)->get(ConcretePriceForm::FIELD_PRICE)->getData();

        $priceTransfer = (new ZedProductPriceTransfer())
            ->setIdProduct($idProduct)
            ->setPrice($price);

        return $priceTransfer;
    }

    /**
     * @param \Symfony\Component\Form\FormInterface $form
     *
     * @return \Generated\Shared\Transfer\StockProductTransfer[]
     */
    public function buildProductStockCollectionTransfer(FormInterface $form)
    {
        $result = [];
        $sku = $form->get(ProductFormAdd::FIELD_SKU)->getData();

        foreach ($form->get(ProductFormAdd::FORM_PRICE_AND_STOCK) as $stockForm) {
            $stockData = $stockForm->getData();
            $type = $stockForm->get(StockForm::FIELD_TYPE)->getData();

            $stockTransfer = (new StockProductTransfer())
                ->fromArray($stockData, true)
                ->setSku($sku)
                ->setStockType($type);

            $result[] = $stockTransfer;
        }

        return $result;
    }

}
