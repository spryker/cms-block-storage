<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Unit\Spryker\Zed\Ratepay\Business\Api\Converter;

use Generated\Shared\Transfer\AddressTransfer;

class AddressMapperTest extends AbstractMapperTest
{

    /**
     * @return void
     */
    public function testMapper()
    {
        $addressTransfer = new AddressTransfer();
        $addressTransfer->setCity('s1')
            ->setIso2Code('iso2')
            ->setAddress1('addr1')
            ->setAddress2('addr2')
            ->setZipCode('zip')
            ->setFirstName('fn')
            ->setLastName('ln');
        $this->mapperFactory
            ->getAddressMapper(
                $addressTransfer,
                'BILLING'
            )
            ->map();

        $this->assertEquals('s1', $this->requestTransfer->getBillingAddress()->getCity());
        $this->assertEquals('iso2', $this->requestTransfer->getBillingAddress()->getCountryCode());
        $this->assertEquals('addr1', $this->requestTransfer->getBillingAddress()->getStreet());
        $this->assertEquals('addr2', $this->requestTransfer->getBillingAddress()->getStreetNumber());
        $this->assertEquals('zip', $this->requestTransfer->getBillingAddress()->getZipCode());
        $this->assertNull($this->requestTransfer->getBillingAddress()->getFirstName());
        $this->assertNull($this->requestTransfer->getBillingAddress()->getLastName());
        $this->mapperFactory
            ->getAddressMapper(
                $addressTransfer,
                'DELIVERY'
            )
            ->map();

        $this->assertEquals('fn', $this->requestTransfer->getShippingAddress()->getFirstName());
        $this->assertEquals('ln', $this->requestTransfer->getShippingAddress()->getLastName());
    }

}
