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
use SprykerFeature\Zed\Url\Business\Exception\UrlExistsException;

interface ProductManagerInterface
{

    /**
     * @param string $sku
     *
     * @return bool
     */
    public function hasAbstractProduct($sku);

    /**
     * @param AbstractProductInterface $abstractProductTransfer
     *
     * @throws AbstractProductExistsException
     *
     * @return int
     */
    public function createAbstractProduct(AbstractProductInterface $abstractProductTransfer);

    /**
     * @param string $sku
     *
     * @throws MissingProductException
     *
     * @return int
     */
    public function getAbstractProductIdBySku($sku);

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
    );

    /**
     * @param ConcreteProductInterface $concreteProductTransfer
     * @param int $idAbstractProduct
     *
     * @throws ConcreteProductExistsException
     *
     * @return int
     */
    public function createConcreteProduct(ConcreteProductInterface $concreteProductTransfer, $idAbstractProduct);

    /**
     * @param string $sku
     *
     * @return bool
     */
    public function hasConcreteProduct($sku);

    /**
     * @param string $sku
     *
     * @throws MissingProductException
     *
     * @return int
     */
    public function getConcreteProductIdBySku($sku);

    /**
     * @param ConcreteProductInterface $concreteProductTransfer
     * @param LocaleTransfer $locale
     *
     * @throws ConcreteProductAttributesExistException
     *
     * @return int
     */
    public function createConcreteProductAttributes(
        ConcreteProductInterface $concreteProductTransfer,
        LocaleTransfer $locale
    );

    /**
     * @param int $idAbstractProduct
     */
    public function touchProductActive($idAbstractProduct);

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
    public function createProductUrl($sku, $url, LocaleTransfer $locale);

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
    public function createProductUrlByIdProduct($idAbstractProduct, $url, LocaleTransfer $locale);

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
    public function createAndTouchProductUrl($sku, $url, LocaleTransfer $locale);

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
    public function createAndTouchProductUrlByIdProduct($idAbstractProduct, $url, LocaleTransfer $locale);

    /**
     * @param string $sku
     *
     * @throws MissingProductException
     *
     * @return float
     */
    public function getEffectiveTaxRateForConcreteProduct($sku);

    /**
     * @param string $sku
     *
     * @throws MissingProductException
     *
     * @return int
     */
    public function getAbstractProductIdByConcreteSku($sku);

}
