<?php

namespace SprykerFeature\Shared\ProductFrontendExporterConnector\Code\KeyBuilder;

use SprykerFeature\Shared\FrontendExporter\Code\KeyBuilder\SharedResourceKeyBuilder;
use SprykerFeature\Shared\Product\ProductConfig;

abstract class SharedAbstractProductResourceKeyBuilder extends SharedResourceKeyBuilder
{
    /**
     * @return string
     */
    protected function getResourceType()
    {
        return ProductConfig::RESOURCE_TYPE_ABSTRACT_PRODUCT;
    }
}
