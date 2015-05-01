<?php

namespace SprykerFeature\Zed\CmsExporter\Business\Builder;

use SprykerFeature\Shared\UrlExporter\Code\KeyBuilder\ResourceKeyBuilder as SharedKeyBuilder;

class ResourceKeyBuilder extends SharedKeyBuilder
{
    /**
     * @return string
     */
    protected function getResourceType()
    {
        return 'page';
    }
}
