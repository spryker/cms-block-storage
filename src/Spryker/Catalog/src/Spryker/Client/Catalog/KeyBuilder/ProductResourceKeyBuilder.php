<?php

/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace Spryker\Client\Catalog\KeyBuilder;

use Spryker\Shared\Collector\Code\KeyBuilder\SharedResourceKeyBuilder;
use Spryker\Shared\Product\ProductConstants;

class ProductResourceKeyBuilder extends SharedResourceKeyBuilder
{

    /**
     * @return string
     */
    protected function getResourceType()
    {
        return ProductConstants::RESOURCE_TYPE_ABSTRACT_PRODUCT;
    }

}
