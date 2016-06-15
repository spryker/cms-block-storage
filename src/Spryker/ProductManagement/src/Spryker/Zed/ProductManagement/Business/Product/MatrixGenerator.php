<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Product\Business\ProductManagement;

use Generated\Shared\Transfer\ProductAbstractTransfer;
use Generated\Shared\Transfer\ZedProductConcreteTransfer;

class MatrixGenerator
{

    const TOKENS = 'tokens';
    const SKU = 'sku';

    const SKU_ABSTRACT_SEPARATOR = '-';
    const SKU_TYPE_SEPARATOR = '-';
    const SKU_VALUE_SEPARATOR = '_';

    /**
     * @param array $orderedTokenCollection
     *
     * @return string
     */
    protected function generateSkuFromTokens(array $orderedTokenCollection)
    {
        $sku = '';
        for ($a=0; $a<count($orderedTokenCollection); $a++) {
            foreach ($orderedTokenCollection[$a] as $type => $value) {
                $sku .= $type . self::SKU_TYPE_SEPARATOR . $value . self::SKU_VALUE_SEPARATOR;
            }
        }

        return rtrim($sku, self::SKU_VALUE_SEPARATOR);
    }

    /**
     * @param string $abstractSku
     * @param string $concreteSku
     *
     * @return string
     */
    protected function formatConcreteSku($abstractSku, $concreteSku)
    {
        return sprintf(
            '%s%s%s',
            $abstractSku,
            self::SKU_ABSTRACT_SEPARATOR,
            $concreteSku
        );
    }

    /**
     * @param array $attributeCollection
     * @param array $current
     * @param int $attributeCount
     *
     * @return array
     */
    protected function collectTokens(array $attributeCollection, array $current, $attributeCount)
    {
        $tokens = [];
        for ($a = 0; $a < $attributeCount; $a++) {
            list($type, $value) = each($attributeCollection[$a][$current[$a]]);
            $tokens[$type] = $value;
        }

        $orderedTokens = $this->sortTokens($tokens);
        $sku = $this->generateSkuFromTokens($orderedTokens);

        return [
            self::TOKENS => $orderedTokens,
            self::SKU => $sku
        ];
    }

    /**
     * @param array $unorderedTokenCollection
     *
     * @return array
     */
    protected function sortTokens(array $unorderedTokenCollection)
    {
        ksort($unorderedTokenCollection, SORT_STRING | SORT_FLAG_CASE);

        $orderedTokens = [];
        foreach ($unorderedTokenCollection as $type => $value) {
            $orderedTokens[] = [$type => $value];
        }

        return $orderedTokens;
    }

    /**
     * @param array $attributeCollection
     *
     * @return array
     */
    protected function convertAttributesIntoTokens(array $attributeCollection)
    {
        $attributes = [];
        foreach ($attributeCollection as $attributeType => $attributeValueSet) {
            $typeAttributesValues = [];
            foreach ($attributeValueSet as $name => $value) {
                $typeAttributesValues[] = [$attributeType => $name];
            }

            $attributes[] = $typeAttributesValues;
        }

        return $attributes;
    }

    /**
     * @param array $tokenCollection
     *
     * @return array
     */
    protected function convertTokensIntoAttributes(array $tokenCollection)
    {
        $attributes = [];
        for ($a=0; $a<count($tokenCollection); $a++) {
            foreach ($tokenCollection[$a] as $attributeType => $attributeValue) {
                $attributes[$attributeType] = $attributeValue;
            }
        }

        return $attributes;
    }

    /**
     * @param array $tokenAttributeCollection
     *
     * @return array
     */
    public function generateTokens(array $tokenAttributeCollection)
    {
        $attributeCount = count($tokenAttributeCollection);
        $current = array_pad([], $attributeCount, 0);
        $changeIndex = 0;

        $result = [];
        while ($changeIndex < $attributeCount) {
            $result[] = $this->collectTokens($tokenAttributeCollection, $current, $attributeCount);
            $changeIndex = 0;

            while ($changeIndex < $attributeCount) {
                $current[$changeIndex]++;

                if ($current[$changeIndex] === count($tokenAttributeCollection[$changeIndex])) {
                    $current[$changeIndex] = 0;
                    $changeIndex++;
                } else {
                    break;
                }
            }
        }

        return $result;
    }

    /**
     * @param \Generated\Shared\Transfer\ProductAbstractTransfer $productAbstractTransfer
     * @param array $attributeCollection
     *
     * @return array
     */
    public function generate(ProductAbstractTransfer $productAbstractTransfer, array $attributeCollection)
    {
        $tokenCollection = $this->generateTokens(
            $this->convertAttributesIntoTokens($attributeCollection)
        );

        $result = [];
        foreach ($tokenCollection as $token) {
            $sku = $this->formatConcreteSku(
                $productAbstractTransfer->requireSku()->getSku(),
                $token[self::SKU]
            );
            $attributeTokens = $this->convertTokensIntoAttributes($token[self::TOKENS]);

            $result[] = $this->createProductConcreteTransfer($productAbstractTransfer, $sku, $attributeTokens);
        }

        return $result;
    }

    /**
     * @param \Generated\Shared\Transfer\ProductAbstractTransfer $productAbstractTransfer
     * @param string $concreteSku
     * @param array $attributeTokens
     *
     * @return \Generated\Shared\Transfer\ZedProductAbstractTransfer
     */
    protected function createProductConcreteTransfer(
        ProductAbstractTransfer $productAbstractTransfer,
        $concreteSku,
        array $attributeTokens
    ) {
        return (new ZedProductConcreteTransfer())
            ->fromArray($productAbstractTransfer->toArray(), true)
            ->setSku($concreteSku)
            ->setAbstractSku($productAbstractTransfer->getSku())
            ->setFkProductAbstract($productAbstractTransfer->getIdProductAbstract())
            ->setAttributes($attributeTokens);
    }

}
