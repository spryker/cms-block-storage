<?php

/**
 * This file is part of the Spryker Demoshop.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Spryker\Yves\Product\Builder;

use Generated\Shared\Transfer\StorageAttributeMapTransfer;
use Generated\Shared\Transfer\StorageProductTransfer;
use Spryker\Client\Product\ProductClientInterface;
use Spryker\Shared\Product\ProductConstants;

class AttributeVariantBuilder implements AttributeVariantBuilderInterface
{

    /**
     * @var \Spryker\Client\Product\ProductClientInterface
     */
    protected $productClient;

    /**
     * @var array
     */
    protected $attributeMap = [];

    /**
     * @var \Spryker\Yves\Product\Builder\ImageSetBuilderInterface
     */
    protected $imageSetBuilder;

    /**
     * @param \Spryker\Client\Product\ProductClientInterface $productClient
     * @param \Spryker\Yves\Product\Builder\ImageSetBuilderInterface $imageSetBuilder
     */
    public function __construct(ProductClientInterface $productClient, ImageSetBuilderInterface $imageSetBuilder)
    {
        $this->productClient = $productClient;
        $this->imageSetBuilder = $imageSetBuilder;
    }

    /**
     * @param \Generated\Shared\Transfer\StorageProductTransfer $storageProductTransfer
     *
     * @return \Generated\Shared\Transfer\StorageProductTransfer
     */
    public function setSuperAttributes(StorageProductTransfer $storageProductTransfer)
    {
        $attributeMap = $this->getAttributeMapFromStorage($storageProductTransfer->getId());
        if (count($attributeMap) === 0) {
            return $storageProductTransfer;
        }

        $storageAttributeMapTransfer = $this->mapStorageAttributeMap($attributeMap);
        if (count($storageAttributeMapTransfer->getProductConcreteIds()) === 1 || count($storageAttributeMapTransfer->getSuperAttributes()) === 0) {
            return $this->getFirstProductVariant($storageProductTransfer, $storageAttributeMapTransfer);
        }

        $storageProductTransfer->setSuperAttributes(
            $storageAttributeMapTransfer->getSuperAttributes()
        );

        return $storageProductTransfer;
    }

    /**
     * @param array $selectedAttributes
     * @param \Generated\Shared\Transfer\StorageProductTransfer $storageProductTransfer
     *
     * @return \Generated\Shared\Transfer\StorageProductTransfer
     */
    public function setSelectedVariants(array $selectedAttributes, StorageProductTransfer $storageProductTransfer)
    {
        $selectedVariantNode = $this->getSelectedVariantNode($selectedAttributes, $storageProductTransfer->getId());

        if ($this->isProductConcreteNodeReached($selectedVariantNode)) {
            $idProductConcrete = $this->extractIdOfProductConcrete($selectedVariantNode);
            return $this->mergeAbstractAndConcreteProducts($idProductConcrete, $storageProductTransfer);
        }

        return $this->setAvailableAttributes($selectedVariantNode, $storageProductTransfer);
    }

    /**
     * @param array $selectedAttributes
     * @param int $idProductAbstract
     *
     * @return array
     */
    protected function getSelectedVariantNode(array $selectedAttributes, $idProductAbstract)
    {
        $attributeMap = $this->getAttributeMapFromStorage($idProductAbstract);

        if (count($attributeMap) === 0) {
            return [];
        }

        $storageAttributeMapTransfer = $this->mapStorageAttributeMap($attributeMap);

        return $this->buildAttributeMapFromSelected(
            $selectedAttributes,
            $storageAttributeMapTransfer->getAttributeVariants()
        );
    }

    /**
     * @param int $idProductConcrete
     * @param \Generated\Shared\Transfer\StorageProductTransfer $storageProductTransfer
     *
     * @return \Generated\Shared\Transfer\StorageProductTransfer
     */
    protected function mergeAbstractAndConcreteProducts(
        $idProductConcrete,
        StorageProductTransfer $storageProductTransfer
    ) {

        $productConcrete = $this->getProductConcreteFromStorage($idProductConcrete);

        if (count($productConcrete) === 0) {
            return $storageProductTransfer;
        }

        return $this->mapVariantStorageProductTransfer($storageProductTransfer, $productConcrete);
    }

    /**
     * @param array $selectedVariantNode
     *
     * @return bool
     */
    protected function isProductConcreteNodeReached(array $selectedVariantNode)
    {
        return isset($selectedVariantNode[ProductConstants::VARIANT_LEAF_NODE_ID]);
    }

    /**
     * @param array $selectedVariantNode
     * @param \Generated\Shared\Transfer\StorageProductTransfer $storageProductTransfer
     *
     * @return \Generated\Shared\Transfer\StorageProductTransfer
     */
    protected function setAvailableAttributes(array $selectedVariantNode, StorageProductTransfer $storageProductTransfer)
    {
        $storageProductTransfer->setAvailableAttributes($this->findAvailableAttributes($selectedVariantNode));

        return $storageProductTransfer;
    }

    /**
     * @param int $idProductConcrete
     *
     * @return array
     */
    protected function getProductConcreteFromStorage($idProductConcrete)
    {
        return $this->productClient->getProductConcreteByIdForCurrentLocale($idProductConcrete);
    }

    /**
     * @param array $selectedAttributes
     * @param array $attributeVariants
     *
     * @return array
     */
    protected function buildAttributeMapFromSelected(array $selectedAttributes, array $attributeVariants)
    {
        ksort($selectedAttributes);

        $attributePath = $this->buildAttributePath($selectedAttributes);

        return $this->findSelectedNode($attributeVariants, $attributePath);
    }

    /**
     * @param array $selectedNode
     *
     * @return array
     */
    protected function findAvailableAttributes(array $selectedNode)
    {
        static $filteredAttributes = [];

        foreach ($selectedNode as $attributePath => $attributeValue) {
            list($key, $value) = explode(ProductConstants::ATTRIBUTE_MAP_PATH_DELIMITER, $attributePath);
            $filteredAttributes[$key][] = $value;
            if (is_array($value)) {
                return $this->findAvailableAttributes($value);
            }
        }

        return $filteredAttributes;
    }

    /**
     * @param array $attributeMap
     * @param array $selectedAttributes
     *
     * @return array
     */
    protected function findSelectedNode(array $attributeMap, array $selectedAttributes)
    {
        static $selectedNode = [];

        $selectedKey = array_shift($selectedAttributes);
        foreach ($attributeMap as $variantKey => $variant) {
            if ($variantKey != $selectedKey) {
                continue;
            }

            $selectedNode = $variant;
            return $this->findSelectedNode($variant, $selectedAttributes);
        }

        if (count($selectedAttributes) > 0) {
            $this->findSelectedNode($attributeMap, $selectedAttributes);
        }

        return $selectedNode;
    }

    /**
     * @param array $selectedVariantNode
     *
     * @return int
     */
    protected function extractIdOfProductConcrete(array $selectedVariantNode)
    {
        if (is_array($selectedVariantNode[ProductConstants::VARIANT_LEAF_NODE_ID])) {
            return array_shift($selectedVariantNode[ProductConstants::VARIANT_LEAF_NODE_ID]);
        }

        return $selectedVariantNode[ProductConstants::VARIANT_LEAF_NODE_ID];
    }

    /**
     * @param array $selectedAttributes
     *
     * @return array
     */
    protected function buildAttributePath(array $selectedAttributes)
    {
        $attributePath = [];
        foreach ($selectedAttributes as $attributeName => $attributeValue) {
            if (!$attributeValue) {
                continue;
            }

            $attributePath[] = $attributeName . ProductConstants::ATTRIBUTE_MAP_PATH_DELIMITER . $attributeValue;
        }
        return $attributePath;
    }

    /**
     * @param int $idProductConcrete
     *
     * @return array
     */
    protected function getAttributeMapFromStorage($idProductConcrete)
    {
        if (!isset($this->attributeMap[$idProductConcrete])) {
            $this->attributeMap[$idProductConcrete] = $this->productClient->getAttributeMapByIdProductAbstractForCurrectLocale($idProductConcrete);
        }

        return $this->attributeMap[$idProductConcrete];
    }

    /**
     * @param \Generated\Shared\Transfer\StorageProductTransfer $storageProductTransfer
     * @param array $productConcrete
     *
     * @return StorageProductTransfer;
     */
    protected function mapVariantStorageProductTransfer(StorageProductTransfer $storageProductTransfer, array $productConcrete)
    {
        $storageProductTransfer->fromArray($productConcrete, true);
        $storageProductTransfer->setImages(
            $this->imageSetBuilder->getDisplayImagesFromPersistedProduct($productConcrete)
        );
        $storageProductTransfer->setIsVariant(true);

        return $storageProductTransfer;
    }

    /**
     * @param array $attributeMap
     *
     * @return \Generated\Shared\Transfer\StorageAttributeMapTransfer
     */
    protected function mapStorageAttributeMap(array $attributeMap)
    {
        $storageAttributeMapTransfer = new StorageAttributeMapTransfer();
        $storageAttributeMapTransfer->fromArray($attributeMap, true);

        return $storageAttributeMapTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\StorageProductTransfer $storageProductTransfer
     * @param \Generated\Shared\Transfer\StorageAttributeMapTransfer $storageAttributeMapTransfer
     *
     * @return \Generated\Shared\Transfer\StorageProductTransfer
     */
    protected function getFirstProductVariant(
        StorageProductTransfer $storageProductTransfer,
        StorageAttributeMapTransfer $storageAttributeMapTransfer
    ) {
        $productConcreteIds = $storageAttributeMapTransfer->getProductConcreteIds();
        $idProductConcrete = array_shift($productConcreteIds);
        $productConcrete = $this->getProductConcreteFromStorage($idProductConcrete);

        if (count($productConcrete) === 0){
            return $storageProductTransfer;
        }

        return $this->mapVariantStorageProductTransfer($storageProductTransfer, $productConcrete);
    }

}
