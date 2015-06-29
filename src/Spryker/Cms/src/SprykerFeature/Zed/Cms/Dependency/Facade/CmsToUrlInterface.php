<?php
/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace SprykerFeature\Zed\Cms\Dependency\Facade;

use Propel\Runtime\Exception\PropelException;
use Generated\Shared\Transfer\UrlTransfer;
use SprykerFeature\Zed\Url\Business\Exception\UrlExistsException;

interface CmsToUrlInterface
{
    /**
     * @param string $url
     * @param string $resourceType
     * @param int $idResource
     *
     * @return UrlTransfer
     * @throws PropelException
     * @throws UrlExistsException
     */
    public function createUrlForCurrentLocale($url, $resourceType, $idResource);
}
