<?php

/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace Spryker\Zed\Country\Business;

interface RegionManagerInterface
{

    /**
     * @param string $isoCode
     * @param int $fkCountry
     * @param string $regionName
     *
     * @return int
     */
    public function createRegion($isoCode, $fkCountry, $regionName);

}
