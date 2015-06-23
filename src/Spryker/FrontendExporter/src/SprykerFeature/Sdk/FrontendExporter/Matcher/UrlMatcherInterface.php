<?php
/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace SprykerFeature\Sdk\FrontendExporter\Matcher;

use SprykerFeature\Yves\FrontendExporter\Business\Model\UrlResource;

/**
 * Interface UrlMatcherInterface
 * @package SprykerFeature\Yves\YvesExport\Matcher
 */
interface UrlMatcherInterface
{
    /**
     * @param string $url
     * @param string $localeName
     * @return UrlResource
     */
    public function matchUrl($url, $localeName);
}