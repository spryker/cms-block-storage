<?php

namespace SprykerFeature\Sdk\Catalog\Model\Builder;

use Elastica\Filter\Nested;

/**
 * Class FilterBuilder
 * @package SprykerFeature\Sdk\Catalog\Model\Builder
 */
interface NestedFilterBuilderInterface
{
    /**
     * @param string $fieldName
     * @param string $nestedFieldName
     * @param string $nestedFieldValue
     * @return Nested
     */
    public function createNestedTermFilter($fieldName, $nestedFieldName, $nestedFieldValue);

    /**
     * @param string $fieldName
     * @param string $nestedFieldName
     * @param array  $nestedFieldValues
     * @return Nested
     */
    public function createNestedTermsFilter($fieldName, $nestedFieldName, array $nestedFieldValues);

    /**
     * @param string $fieldName
     * @param string $nestedFieldName
     * @param string $minValue
     * @param string $maxValue
     * @param string $greaterParam
     * @param string $lessParam
     * @return Nested
     */
    public function createNestedRangeFilter($fieldName, $nestedFieldName, $minValue, $maxValue, $greaterParam = 'gte', $lessParam = 'lte');
}