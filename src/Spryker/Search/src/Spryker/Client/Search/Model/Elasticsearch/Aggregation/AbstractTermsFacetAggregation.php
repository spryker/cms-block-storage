<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Client\Search\Model\Elasticsearch\Aggregation;

use Elastica\Aggregation\AbstractTermsAggregation;
use Generated\Shared\Transfer\FacetConfigTransfer;

abstract class AbstractTermsFacetAggregation extends AbstractFacetAggregation
{

    const AGGREGATION_PARAM_SIZE = 'size';

    /**
     * @param \Elastica\Aggregation\AbstractTermsAggregation $aggregation
     * @param int|null $size
     *
     * @return void
     */
    protected function setTermsAggregationSize(AbstractTermsAggregation $aggregation, $size)
    {
        if ($size === null) {
            return;
        }

        $aggregation->setSize($size);
    }

    /**
     * @param \Generated\Shared\Transfer\FacetConfigTransfer $facetConfigTransfer
     *
     * @return int|null
     */
    protected function getSizeParam(FacetConfigTransfer $facetConfigTransfer)
    {
        if (isset($facetConfigTransfer->getAggregationParams()[static::AGGREGATION_PARAM_SIZE])) {
            return $facetConfigTransfer->getAggregationParams()[static::AGGREGATION_PARAM_SIZE];
        }

        return null;
    }

    /**
     * @deprecated Use getSizeParam instead.
     * Will be removed with the next major release.
     *
     * @param \Generated\Shared\Transfer\FacetConfigTransfer $facetConfigTransfer
     *
     * @return int|null
     */
    protected function getSizeParamFallback(FacetConfigTransfer $facetConfigTransfer)
    {
        $size = $this->getSizeParam($facetConfigTransfer);

        if ($size !== null) {
            return $size;
        }

        return $facetConfigTransfer->getSize();
    }

}
