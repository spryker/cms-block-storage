<?php

/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace Spryker\Zed\Country\Business;

use Generated\Shared\Transfer\CountryCollectionTransfer;
use Generated\Shared\Transfer\CountryTransfer;
use Spryker\Zed\Country\Business\Exception\CountryExistsException;
use Spryker\Zed\Country\Business\Exception\MissingCountryException;
use Spryker\Zed\Country\Persistence\CountryQueryContainerInterface;
use Orm\Zed\Country\Persistence\SpyCountry;

class CountryManager implements CountryManagerInterface
{

    /**
     * @var CountryQueryContainerInterface
     */
    protected $countryQueryContainer;

    /**
     * @param \Spryker\Zed\Country\Persistence\CountryQueryContainerInterface $countryQueryContainer
     */
    public function __construct(
        CountryQueryContainerInterface $countryQueryContainer
    ) {
        $this->countryQueryContainer = $countryQueryContainer;
    }

    /**
     * @param string $iso2code
     *
     * @return bool
     */
    public function hasCountry($iso2code)
    {
        $query = $this->countryQueryContainer->queryCountryByIso2Code($iso2code);

        return $query->count() > 0;
    }

    /**
     * @return \Generated\Shared\Transfer\CountryCollectionTransfer
     */
    public function getCountryCollection()
    {
        $countries = $this->countryQueryContainer->queryCountries()->find();
        $countryCollectionTransfer = new CountryCollectionTransfer();

        foreach ($countries as $country) {
            $countryTransfer = (new CountryTransfer())->fromArray($country->toArray(), true);
            $countryCollectionTransfer->addCountries($countryTransfer);
        }

        return $countryCollectionTransfer;
    }

    /**
     * @param string $countryName
     *
     * @deprecated Use getPreferredCountryByName()
     *
     * @return \Generated\Shared\Transfer\CountryTransfer
     */
    public function getPreferedCountryByName($countryName)
    {
        return $this->getPreferredCountryByName($countryName);
    }

    /**
     * @param string $countryName
     *
     * @return \Generated\Shared\Transfer\CountryTransfer
     */
    public function getPreferredCountryByName($countryName)
    {
        $country = $this->countryQueryContainer->queryCountries()->findOneByName($countryName);

        if ($country === null) {
            return new CountryTransfer();
        }

        $countryTransfer = (new CountryTransfer())->fromArray($country->toArray(), true);

        return $countryTransfer;
    }

    /**
     * @param string $iso2code
     * @param array $countryData
     *
     * @deprecated Use saveCountry() instead.
     *
     * @throws \Spryker\Zed\Country\Business\Exception\CountryExistsException
     *
     * @return int
     */
    public function createCountry($iso2code, array $countryData)
    {
        $this->checkCountryDoesNotExist($iso2code);

        $country = new SpyCountry();
        $country
            ->setName($countryData['name'])
            ->setPostalCodeMandatory($countryData['postal_code_mandatory'])
            ->setPostalCodeRegex($countryData['postal_code_regex'])
            ->setIso2Code($iso2code)
            ->setIso3Code($countryData['iso3_code']);

        $country->save();

        return $country->getIdCountry();
    }

    /**
     * @param \Generated\Shared\Transfer\CountryTransfer $countryTransfer
     *
     * @return int
     */
    public function saveCountry(CountryTransfer $countryTransfer)
    {
        return $this->createCountry($countryTransfer->getIso2Code(), $countryTransfer->toArray());
    }

    /**
     * @param string $iso2code
     *
     * @throws \Spryker\Zed\Country\Business\Exception\MissingCountryException
     *
     * @return int
     */
    public function getIdCountryByIso2Code($iso2code)
    {
        $query = $this->countryQueryContainer->queryCountryByIso2Code($iso2code);
        $country = $query->findOne();

        if (!$country) {
            throw new MissingCountryException();
        }

        return $country->getIdCountry();
    }

    /**
     * @param string $iso2code
     *
     * @throws \Spryker\Zed\Country\Business\Exception\CountryExistsException
     *
     * @return void
     */
    protected function checkCountryDoesNotExist($iso2code)
    {
        if ($this->hasCountry($iso2code)) {
            throw new CountryExistsException();
        }
    }

}
