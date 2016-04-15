<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Sales\Dependency\Facade;

class SalesToCountryBridge implements SalesToCountryInterface
{

    /**
     * @var \Spryker\Zed\Country\Business\CountryFacade
     */
    protected $countryFacade;

    /**
     * SalesToCountryBridge constructor.
     *
     * @param \Spryker\Zed\Country\Business\CountryFacade $countryFacade
     */
    public function __construct($countryFacade)
    {
        $this->countryFacade = $countryFacade;
    }

    /**
     * @param string $iso2Code
     *
     * @return int
     */
    public function getIdCountryByIso2Code($iso2Code)
    {
        return $this->countryFacade->getIdCountryByIso2Code($iso2Code);
    }

}
