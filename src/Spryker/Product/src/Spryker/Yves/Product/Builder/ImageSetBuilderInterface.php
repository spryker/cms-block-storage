<?php

/**
 * This file is part of the Spryker Demoshop.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Spryker\Yves\Product\Builder;

interface ImageSetBuilderInterface
{

    /**
     * @param array $persistedProductData
     *
     * @return array
     */
    public function getDisplayImagesFromPersistedProduct(array $persistedProductData);

}
