<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductAttribute\Business\Model\Product\Mapper;

use Spryker\Zed\ProductAttribute\Dependency\Service\ProductAttributeToUtilEncodingInterface;
use Spryker\Zed\ProductAttribute\ProductAttributeConfig;

class ProductAttributeMapper implements ProductAttributeMapperInterface
{

    /**
     * @var \Spryker\Zed\ProductAttribute\Dependency\Service\ProductAttributeToUtilEncodingInterface
     */
    protected $serviceEncoding;

    /**
     * @param \Spryker\Zed\ProductAttribute\Dependency\Service\ProductAttributeToUtilEncodingInterface $serviceEncoding
     */
    public function __construct(ProductAttributeToUtilEncodingInterface $serviceEncoding)
    {
        $this->serviceEncoding = $serviceEncoding;
    }

    /**
     * @param array $attributes
     *
     * @return string
     */
    public function encodeJsonAttributes(array $attributes)
    {
        return (string)$this->serviceEncoding->encodeJson($attributes);
    }

    /**
     * @param string $attributesJson
     *
     * @return array
     */
    public function decodeJsonAttributes($attributesJson)
    {
        return (array)$this->serviceEncoding->decodeJson($attributesJson, true);
    }

    /**
     * @param array|\Orm\Zed\Product\Persistence\SpyProductAttributeKey[]|\Propel\Runtime\Collection\ObjectCollection $metaAttributeCollection
     *
     * @return array
     */
    public function mapMetaAttributes($metaAttributeCollection)
    {
        $results = [];
        foreach ($metaAttributeCollection as $metaAttribute) {
            unset($metaAttribute[ProductAttributeConfig::ID_PRODUCT_ATTRIBUTE_KEY]);
            $results[$metaAttribute[ProductAttributeConfig::KEY]] = $metaAttribute;
        }

        return $results;
    }

    /**
     * @param array|\Orm\Zed\Product\Persistence\SpyProductAttributeKey[]|\Propel\Runtime\Collection\ObjectCollection $metaAttributeCollection
     *
     * @return array
     */
    public function maSuggestKeys($metaAttributeCollection)
    {
        $results = [];

        foreach ($metaAttributeCollection as $keyEntity) {
            unset($keyEntity[ProductAttributeConfig::ID_PRODUCT_ATTRIBUTE_KEY]);
            $results[] = $keyEntity;
        }

        return $results;
    }

    /**
     * $productAttributes format
     * [
     *   [_] => [key=>value, key2=>value2]
     *   [46] => [key=>value]
     *   [66] => [key3=>value3, key5=value5]
     * ]
     *
     * @see ProductAttributeConfig::DEFAULT_LOCALE
     *
     * @param array $productAttributes
     *
     * @return array
     */
    public function extractKeysFromAttributes(array $productAttributes)
    {
        $keys = [];
        foreach ($productAttributes as $idLocale => $localizedAttributes) {
            $keys = array_merge($keys, array_keys($localizedAttributes));
        }

        return array_unique($keys);
    }

    /**
     * $productAttributes format
     * [
     *   [_] => [key=>value, key2=>value2]
     *   [46] => [key=>value]
     *   [66] => [key3=>value3, key5=value5]
     * ]
     *
     * @see ProductAttributeConfig::DEFAULT_LOCALE
     *
     * @param array $productAttributes
     *
     * @return array
     */
    public function extractValuesFromAttributes(array $productAttributes)
    {
        $values = [];
        foreach ($productAttributes as $idLocale => $localizedAttributes) {
            $values = array_merge($values, array_values($localizedAttributes));
        }

        return array_unique($values);
    }

}
