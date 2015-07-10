<?php

/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace SprykerFeature\Zed\Product\Business;

use Generated\Shared\Transfer\LocaleTransfer;
use Generated\Shared\Transfer\UrlTransfer;
use Propel\Runtime\Exception\PropelException;
use SprykerEngine\Shared\Kernel\Messenger\MessengerInterface;
use SprykerEngine\Zed\Kernel\Business\AbstractFacade;
use SprykerFeature\Zed\Product\Business\Exception\AbstractProductAttributesExistException;
use SprykerFeature\Zed\Product\Business\Exception\AbstractProductExistsException;
use SprykerFeature\Zed\Product\Business\Exception\AttributeExistsException;
use SprykerFeature\Zed\Product\Business\Exception\AttributeTypeExistsException;
use SprykerFeature\Zed\Product\Business\Exception\ConcreteProductAttributesExistException;
use SprykerFeature\Zed\Product\Business\Exception\ConcreteProductExistsException;
use SprykerFeature\Zed\Product\Business\Exception\MissingAttributeTypeException;
use SprykerFeature\Zed\Product\Business\Exception\MissingProductException;
use SprykerFeature\Zed\Product\Business\Model\ProductBatchResult;
use SprykerFeature\Zed\Url\Business\Exception\UrlExistsException;

/**
 * @method ProductDependencyContainer getDependencyContainer()
 */
class ProductFacade extends AbstractFacade
{

    /**
     * @param \SplFileInfo $file
     *
     * @return ProductBatchResult
     */
    public function importProductsFromFile(\SplFileInfo $file)
    {
        return $this->getDependencyContainer()
            ->createProductImporter()
            ->importFile($file);
    }

    /**
     * @param string $uploadedFilename
     *
     * @return \SplFileInfo
     */
    public function importUploadedFile($uploadedFilename)
    {
        return $this->getDependencyContainer()
            ->createHttpFileImporter()
            ->receiveUploadedFile($uploadedFilename);
    }

    /**
     * @param string $sku
     *
     * @throws MissingProductException
     *
     * @return int
     */
    public function getAbstractProductIdBySku($sku)
    {
        return $this->getDependencyContainer()->createProductManager()->getAbstractProductIdBySku($sku);
    }

    /**
     * @param string $sku
     *
     * @throws MissingProductException
     *
     * @return int
     */
    public function getConcreteProductIdBySku($sku)
    {
        return $this->getDependencyContainer()->createProductManager()->getConcreteProductIdBySku($sku);
    }

    /**
     * @param string $sku
     *
     * @throws MissingProductException
     *
     * @return int
     */
    public function getAbstractProductIdByConcreteSku($sku)
    {
        return $this->getDependencyContainer()->createProductManager()->getAbstractProductIdByConcreteSku($sku);
    }

    /**
     * @param string $sku
     *
     * @throws MissingProductException
     *
     * @return float
     */
    public function getEffectiveTaxRateForConcreteProduct($sku)
    {
        return $this->getDependencyContainer()->createProductManager()->getEffectiveTaxRateForConcreteProduct($sku);
    }

    /**
     * @param string $attributeName
     *
     * @return bool
     */
    public function hasAttribute($attributeName)
    {
        $attributeManager = $this->getDependencyContainer()->createAttributeManager();

        return $attributeManager->hasAttribute($attributeName);
    }

    /**
     * @param string $attributeType
     *
     * @return bool
     */
    public function hasAttributeType($attributeType)
    {
        $attributeManager = $this->getDependencyContainer()->createAttributeManager();

        return $attributeManager->hasAttributeType($attributeType);
    }

    /**
     * @param string $name
     * @param string $inputType
     * @param int|null $fkParentAttributeType
     *
     * @throws AttributeTypeExistsException
     *
     * @return int
     */
    public function createAttributeType($name, $inputType, $fkParentAttributeType = null)
    {
        $attributeManager = $this->getDependencyContainer()->createAttributeManager();

        return $attributeManager->createAttributeType($name, $inputType, $fkParentAttributeType);
    }

    /**
     * @param string $attributeName
     * @param string $attributeType
     * @param bool $isEditable
     *
     * @throws AttributeExistsException
     * @throws MissingAttributeTypeException
     *
     * @return int
     */
    public function createAttribute($attributeName, $attributeType, $isEditable = true)
    {
        $attributeManager = $this->getDependencyContainer()->createAttributeManager();

        return $attributeManager->createAttribute($attributeName, $attributeType, $isEditable);
    }

    /**
     * @param string $sku
     *
     * @return bool
     */
    public function hasAbstractProduct($sku)
    {
        $productManager = $this->getDependencyContainer()->createProductManager();

        return $productManager->hasAbstractProduct($sku);
    }

    /**
     * @param string $sku

     *
     * @throws AbstractProductExistsException
     *
     * @return int
     */
    public function createAbstractProduct($sku)
    {
        $productManager = $this->getDependencyContainer()->createProductManager();

        return $productManager->createAbstractProduct($sku);
    }

    /**
     * @param int $idAbstractProduct
     * @param LocaleTransfer $locale
     * @param string $name
     * @param string $attributes
     *
     * @throws AbstractProductAttributesExistException
     *
     * @return int
     */
    public function createAbstractProductAttributes($idAbstractProduct, LocaleTransfer $locale, $name, $attributes)
    {
        $productManager = $this->getDependencyContainer()->createProductManager();

        return $productManager->createAbstractProductAttributes($idAbstractProduct, $locale, $name, $attributes);
    }

    /**
     * @param string $sku
     *
     * @return bool
     */
    public function hasConcreteProduct($sku)
    {
        $productManager = $this->getDependencyContainer()->createProductManager();

        return $productManager->hasConcreteProduct($sku);
    }

    /**
     * @param string $sku
     * @param int $idAbstractProduct
     * @param bool $isActive
     *
     * @throws ConcreteProductExistsException
     *
     * @return int
     */
    public function createConcreteProduct($sku, $idAbstractProduct, $isActive = true)
    {
        $productManager = $this->getDependencyContainer()->createProductManager();

        return $productManager->createConcreteProduct($sku, $idAbstractProduct, $isActive);
    }

    /**
     * @param int $idConcreteProduct
     * @param LocaleTransfer $locale
     * @param string $name
     * @param string $attributes
     *
     * @throws ConcreteProductAttributesExistException
     *
     * @return int
     */
    public function createConcreteProductAttributes($idConcreteProduct, LocaleTransfer $locale, $name, $attributes)
    {
        $productManager = $this->getDependencyContainer()->createProductManager();

        return $productManager->createConcreteProductAttributes($idConcreteProduct, $locale, $name, $attributes);
    }

    /**
     * @param int $idAbstractProduct
     */
    public function touchProductActive($idAbstractProduct)
    {
        $productManager = $this->getDependencyContainer()->createProductManager();

        $productManager->touchProductActive($idAbstractProduct);
    }

    /**
     * @param string $sku
     * @param string $url
     * @param LocaleTransfer $locale
     *
     * @throws PropelException
     * @throws UrlExistsException
     * @throws MissingProductException
     *
     * @return UrlTransfer
     */
    public function createProductUrl($sku, $url, LocaleTransfer $locale)
    {
        return $this->getDependencyContainer()->createProductManager()->createProductUrl($sku, $url, $locale);
    }

    /**
     * @param string $sku
     * @param string $url
     * @param LocaleTransfer $locale
     *
     * @throws PropelException
     * @throws UrlExistsException
     * @throws MissingProductException
     *
     * @return Url
     */
    public function createAndTouchProductUrl($sku, $url, LocaleTransfer $locale)
    {
        return $this->getDependencyContainer()->createProductManager()->createAndTouchProductUrl($sku, $url, $locale);
    }

    /**
     * @param MessengerInterface $messenger
     */
    public function install(MessengerInterface $messenger = null)
    {
        $this->getDependencyContainer()->createInstaller($messenger)->install();
    }

    /**
     * @param string $sku
     *
     * @return string
     *
     * @todo examine this as the product sku mapper is gone
     */
    public function getAbstractSkuFromConcreteProduct($sku)
    {
        return $this->getDependencyContainer()->getProductSkuMapper()->getAbstractSkuFromConcreteProduct($sku);
    }

}
