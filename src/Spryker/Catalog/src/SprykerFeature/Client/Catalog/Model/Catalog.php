<?php

namespace SprykerFeature\Client\Catalog\Model;

use SprykerFeature\Shared\FrontendExporter\Code\KeyBuilder\KeyBuilderInterface;
use SprykerFeature\Shared\KvStorage\Client\ReadInterface;
use SprykerFeature\Client\Catalog\Model\Exception\ProductNotFoundException;

class Catalog implements CatalogInterface
{

    const INDEXKEY_SKU = 'sku';
    const INDEXKEY_ID = 'id';
    const INDEXKEY_VARIETY = 'variety';

    const INDEXKEY_PRODUCT_BUNDLE_SKUS = 'bundleInfoSku';
    const INDEXKEY_PRODUCT_CONFIG_SKUS = 'configInfoSku';

    const PRODUCT_VARIETY_SIMPLE = 'Simple';
    const PRODUCT_VARIETY_CONFIG = 'Config';
    const PRODUCT_VARIETY_BUNDLE = 'Bundle';

    /**
     * @var KeyBuilderInterface
     */
    protected $productKeyBuilder;

    /**
     * @var ReadInterface
     */
    protected $storageReader;

    /**
     * @var string
     */
    protected $locale;

    /**
     * @param KeyBuilderInterface $productKeyBuilder
     * @param ReadInterface       $storageReader
     * @param string              $locale
     */
    public function __construct(
        KeyBuilderInterface $productKeyBuilder,
        ReadInterface $storageReader,
        $locale
    ) {
        $this->productKeyBuilder = $productKeyBuilder;
        $this->storageReader = $storageReader;
        $this->locale = $locale;
    }

    /**
     * @param int $id
     *
     * @return array
     * @throws ProductNotFoundException
     */
    public function getProductDataById($id)
    {
        $productKey = $this->productKeyBuilder->generateKey($id, $this->locale);
        $productFromStorage = $this->storageReader->get($productKey);

        if (empty($productFromStorage)) {
            throw new ProductNotFoundException($id);
        }

        return $productFromStorage;
    }

    /**
     * @param array $ids
     * @param null  $indexByKey
     * 
     * @return array
     */
    public function getProductDataByIds(array $ids, $indexByKey = null)
    {
        $idKeys = [];
        foreach ($ids as $id) {
            $idKeys[] = $this->productKeyBuilder->generateKey($id, $this->locale);
        }
        $productsFromStorage = $this->storageReader->getMulti($idKeys);
        foreach ($productsFromStorage as $key => $product) {
            $productsFromStorage[$key] = $this->mergeAttributes(json_decode($product, true));
        }

        if ($indexByKey) {
            return $this->mapKeysToValue($indexByKey, $productsFromStorage);
        }

        return $productsFromStorage;
    }

    /**
     * @param string $key
     * @param array $productsFromStorage
     *
     * @return array
     */
    protected function mapKeysToValue($key, array $productsFromStorage)
    {
        $productsIndexedById = [];
        foreach ($productsFromStorage as $product) {
            $productsIndexedById[$product[$key]] = $product;
        }

        return $productsIndexedById;
    }

    /**
     * @param array $product
     *
     * @return array|\array[]
     */
    public function getSubProducts(array $product)
    {
        $subProducts = [];
        switch($product[self::INDEXKEY_VARIETY]) {
            case self::PRODUCT_VARIETY_CONFIG :
                return $this->getSubProductsBySkuIndex($product, self::INDEXKEY_PRODUCT_CONFIG_SKUS);
                break;
            case self::PRODUCT_VARIETY_BUNDLE :
                return $this->getSubProductsBySkuIndex($product, self::INDEXKEY_PRODUCT_BUNDLE_SKUS);
                break;
        }

        return $subProducts;
    }

    /**
     * @param array $product
     *
     * @return array
     */
    protected function mergeAttributes(array $product)
    {
        if (isset($product['attributes'])) {
            $productAttributes = $product['attributes'];

            return array_merge($product, $productAttributes);
        }

        return $product;
    }
}
