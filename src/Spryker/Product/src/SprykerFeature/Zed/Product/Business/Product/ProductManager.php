<?php

/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace SprykerFeature\Zed\Product\Business\Product;

use Generated\Shared\Product\AbstractProductInterface;
use Generated\Shared\Product\ConcreteProductInterface;
use Generated\Shared\Transfer\LocaleTransfer;
use Generated\Shared\Transfer\UrlTransfer;
use Propel\Runtime\Exception\PropelException;
use SprykerFeature\Zed\Product\Business\Exception\AbstractProductAttributesExistException;
use SprykerFeature\Zed\Product\Business\Exception\AbstractProductExistsException;
use SprykerFeature\Zed\Product\Business\Exception\ConcreteProductAttributesExistException;
use SprykerFeature\Zed\Product\Business\Exception\ConcreteProductExistsException;
use SprykerFeature\Zed\Product\Business\Exception\MissingProductException;
use SprykerFeature\Zed\Product\Dependency\Facade\ProductToTouchInterface;
use SprykerFeature\Zed\Product\Dependency\Facade\ProductToUrlInterface;
use SprykerFeature\Zed\Product\Persistence\ProductQueryContainerInterface;
use SprykerFeature\Zed\Product\Persistence\Propel\SpyAbstractProduct;
use SprykerFeature\Zed\Product\Persistence\Propel\SpyLocalizedAbstractProductAttributes;
use SprykerFeature\Zed\Product\Persistence\Propel\SpyLocalizedProductAttributes;
use SprykerFeature\Zed\Product\Persistence\Propel\SpyProduct;
use SprykerFeature\Zed\Url\Business\Exception\UrlExistsException;

class ProductManager implements ProductManagerInterface
{

    /**
     * @var ProductQueryContainerInterface
     */
    protected $productQueryContainer;

    /**
     * @var ProductToTouchInterface
     */
    protected $touchFacade;

    /**
     * @var ProductToUrlInterface
     */
    protected $urlFacade;

    /**
     * @param ProductQueryContainerInterface $productQueryContainer
     * @param ProductToTouchInterface $touchFacade
     * @param ProductToUrlInterface $urlFacade
     */
    public function __construct(
        ProductQueryContainerInterface $productQueryContainer,
        ProductToTouchInterface $touchFacade,
        ProductToUrlInterface $urlFacade
    ) {
        $this->productQueryContainer = $productQueryContainer;
        $this->touchFacade = $touchFacade;
        $this->urlFacade = $urlFacade;
    }

    /**
     * @param string $sku
     *
     * @return bool
     */
    public function hasAbstractProduct($sku)
    {
        $abstractProductQuery = $this->productQueryContainer->queryAbstractProductBySku($sku);

        return $abstractProductQuery->count() > 0;
    }

    /**
     * @param AbstractProductInterface $abstractProductTransfer
     *
     * @throws AbstractProductExistsException
     * @throws PropelException
     * 
     * @return int
     */
    public function createAbstractProduct(AbstractProductInterface $abstractProductTransfer)
    {
        $sku = $abstractProductTransfer->getSku();

        $this->checkAbstractProductDoesNotExist($sku);
        $encodedAttributes = $this->encodeAttributes($abstractProductTransfer->getAttributes());

        $abstractProduct = new SpyAbstractProduct();
        $abstractProduct
            ->setAttributes($encodedAttributes)
            ->setSku($sku)
        ;

        $abstractProduct->save();

        return $abstractProduct->getPrimaryKey();
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
        $abstractProduct = $this->productQueryContainer->queryAbstractProductBySku($sku)->findOne();

        if (!$abstractProduct) {
            throw new MissingProductException(
                sprintf(
                    'Tried to retrieve an abstract product with sku %s, but it does not exist.',
                    $sku
                )
            );
        }

        return $abstractProduct->getPrimaryKey();
    }

    /**
     * @param string $sku
     *
     * @throws AbstractProductExistsException
     */
    protected function checkAbstractProductDoesNotExist($sku)
    {
        if ($this->hasAbstractProduct($sku)) {
            throw new AbstractProductExistsException(
                sprintf(
                    'Tried to create an abstract product with sku %s that already exists',
                    $sku
                )
            );
        }
    }

    /**
     * @param AbstractProductInterface $abstractProductTransfer
     * @param LocaleTransfer $locale
     *
     * @throws AbstractProductAttributesExistException
     * @throws PropelException
     * 
     * @return int
     */
    public function createAbstractProductAttributes(
        AbstractProductInterface $abstractProductTransfer,
        LocaleTransfer $locale
    ) {
        $idAbstractProduct = $abstractProductTransfer->getIdAbstractProduct();

        $this->checkAbstractProductAttributesDoNotExist($idAbstractProduct, $locale);
        $encodedAttributes = $this->encodeAttributes($abstractProductTransfer->getLocalizedAttributes());

        $abstractProductAttributesEntity = new SpyLocalizedAbstractProductAttributes();
        $abstractProductAttributesEntity
            ->setFkAbstractProduct($idAbstractProduct)
            ->setFkLocale($locale->getIdLocale())
            ->setName($abstractProductTransfer->getName())
            ->setAttributes($encodedAttributes)
        ;

        $abstractProductAttributesEntity->save();

        return $abstractProductAttributesEntity->getPrimaryKey();
    }

    /**
     * @param int $idAbstractProduct
     * @param LocaleTransfer $locale
     *
     * @throws AbstractProductAttributesExistException
     */
    protected function checkAbstractProductAttributesDoNotExist($idAbstractProduct, $locale)
    {
        if ($this->hasAbstractProductAttributes($idAbstractProduct, $locale)) {
            throw new AbstractProductAttributesExistException(
                sprintf(
                    'Tried to create abstract attributes for abstract product %s, locale id %s, but it already exists',
                    $idAbstractProduct,
                    $locale->getIdLocale()
                )
            );
        }
    }

    /**
     * @param int $idAbstractProduct
     * @param LocaleTransfer $locale
     *
     * @return bool
     */
    protected function hasAbstractProductAttributes($idAbstractProduct, LocaleTransfer $locale)
    {
        $query = $this->productQueryContainer->queryAbstractProductAttributeCollection(
            $idAbstractProduct,
            $locale->getIdLocale()
        );

        return $query->count() > 0;
    }

    /**
     * @param ConcreteProductInterface $concreteProductTransfer
     * @param int $idAbstractProduct
     *
     * @throws ConcreteProductExistsException
     * @throws PropelException
     *
     * @return int
     */
    public function createConcreteProduct(ConcreteProductInterface $concreteProductTransfer, $idAbstractProduct)
    {
        $sku = $concreteProductTransfer->getSku();

        $this->checkConcreteProductDoesNotExist($sku);
        $encodedAttributes = $this->encodeAttributes($concreteProductTransfer->getAttributes());

        $concreteProductEntity = new SpyProduct();
        $concreteProductEntity
            ->setSku($sku)
            ->setFkAbstractProduct($idAbstractProduct)
            ->setAttributes($encodedAttributes)
            ->setIsActive($concreteProductTransfer->getIsActive())
        ;

        $concreteProductEntity->save();

        return $concreteProductEntity->getPrimaryKey();
    }

    /**
     * @param string $sku
     *
     * @throws ConcreteProductExistsException
     */
    protected function checkConcreteProductDoesNotExist($sku)
    {
        if ($this->hasConcreteProduct($sku)) {
            throw new ConcreteProductExistsException(
                sprintf(
                    'Tried to create a concrete product with sku %s, but it already exists',
                    $sku
                )
            );
        }
    }

    /**
     * @param string $sku
     *
     * @return bool
     */
    public function hasConcreteProduct($sku)
    {
        $query = $this->productQueryContainer->queryConcreteProductBySku($sku);

        return $query->count() > 0;
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
        $concreteProduct = $this->productQueryContainer->queryConcreteProductBySku($sku)->findOne();

        if (!$concreteProduct) {
            throw new MissingProductException(
                sprintf(
                    'Tried to retrieve a concrete product with sku %s, but it does not exist',
                    $sku
                )
            );
        }

        return $concreteProduct->getPrimaryKey();
    }

    /**
     * @param ConcreteProductInterface $concreteProductTransfer
     * @param LocaleTransfer $locale
     *
     * @throws ConcreteProductAttributesExistException
     * @throws PropelException
     *
     * @return int
     */
    public function createConcreteProductAttributes(
        ConcreteProductInterface $concreteProductTransfer,
        LocaleTransfer $locale
    ) {
        $idConcreteProduct = $concreteProductTransfer->getIdConcreteProduct();
        $this->checkConcreteProductAttributesDoNotExist($idConcreteProduct, $locale);
        $encodedAttributes = $this->encodeAttributes($concreteProductTransfer->getLocalizedAttributes());

        $productAttributeEntity = new SpyLocalizedProductAttributes();
        $productAttributeEntity
            ->setFkProduct($idConcreteProduct)
            ->setFkLocale($locale->getIdLocale())
            ->setName($concreteProductTransfer->getName())
            ->setAttributes($encodedAttributes)
        ;

        $productAttributeEntity->save();

        return $productAttributeEntity->getPrimaryKey();
    }

    /**
     * @param int $idConcreteProduct
     * @param LocaleTransfer $locale
     *
     * @throws ConcreteProductAttributesExistException
     */
    protected function checkConcreteProductAttributesDoNotExist($idConcreteProduct, LocaleTransfer $locale)
    {
        if ($this->hasConcreteProductAttributes($idConcreteProduct, $locale)) {
            throw new ConcreteProductAttributesExistException(
                sprintf(
                    'Tried to create concrete product attributes for product id %s, locale id %s, but they exist',
                    $idConcreteProduct,
                    $locale->getIdLocale()
                )
            );
        }
    }

    /**
     * @param int $idConcreteProduct
     * @param LocaleTransfer $locale
     *
     * @return bool
     */
    protected function hasConcreteProductAttributes($idConcreteProduct, LocaleTransfer $locale)
    {
        $query = $this->productQueryContainer->queryConcreteProductAttributeCollection(
            $idConcreteProduct,
            $locale->getIdLocale()
        );

        return $query->count() > 0;
    }

    /**
     * @param int $idAbstractProduct
     */
    public function touchProductActive($idAbstractProduct)
    {
        $this->touchFacade->touchActive('abstract_product', $idAbstractProduct);
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
        $idAbstractProduct = $this->getAbstractProductIdBySku($sku);

        return $this->createProductUrlByIdProduct($idAbstractProduct, $url, $locale);
    }

    /**
     * @param int $idAbstractProduct
     * @param string $url
     * @param LocaleTransfer $locale
     *
     * @throws PropelException
     * @throws UrlExistsException
     * @throws MissingProductException
     *
     * @return UrlTransfer
     */
    public function createProductUrlByIdProduct($idAbstractProduct, $url, LocaleTransfer $locale)
    {
        return $this->urlFacade->createUrl($url, $locale, 'abstract_product', $idAbstractProduct);
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
    public function createAndTouchProductUrl($sku, $url, LocaleTransfer $locale)
    {
        $url = $this->createProductUrl($sku, $url, $locale);
        $this->urlFacade->touchUrlActive($url->getIdUrl());

        return $url;
    }

    /**
     * @param int $idAbstractProduct
     * @param string $url
     * @param LocaleTransfer $locale
     *
     * @throws PropelException
     * @throws UrlExistsException
     * @throws MissingProductException
     *
     * @return UrlTransfer
     */
    public function createAndTouchProductUrlByIdProduct($idAbstractProduct, $url, LocaleTransfer $locale)
    {
        $url = $this->createProductUrlByIdProduct($idAbstractProduct, $url, $locale);
        $this->urlFacade->touchUrlActive($url->getIdUrl());

        return $url;
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
        $concreteProduct = $this->productQueryContainer->queryConcreteProductBySku($sku)->findOne();

        if (!$concreteProduct) {
            throw new MissingProductException(
                sprintf(
                    'Tried to retrieve a concrete product with sku %s, but it does not exist.',
                    $sku
                )
            );
        }

        $abstractProduct = $concreteProduct->getSpyAbstractProduct();

        $effectiveTaxRate = 0.0;

        $taxSetEntity = $abstractProduct->getSpyTaxSet();
        if (null === $taxSetEntity) {
            return $effectiveTaxRate;
        }

        foreach ($taxSetEntity->getSpyTaxRates() as $taxRateEntity) {
            $effectiveTaxRate += $taxRateEntity->getRate();
        }

        return $effectiveTaxRate;
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
        $concreteProduct = $this->productQueryContainer->queryConcreteProductBySku($sku)->findOne();

        if (!$concreteProduct) {
            throw new MissingProductException(
                sprintf(
                    'Tried to retrieve a concrete product with sku %s, but it does not exist.',
                    $sku
                )
            );
        }

        return $concreteProduct->getFkAbstractProduct();
    }

    /**
     * @param array $attributes
     *
     * @return string
     */
    protected function encodeAttributes(array $attributes)
    {
        return json_encode($attributes);
    }

}
