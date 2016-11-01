<?php
/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Client\Availability;

use Spryker\Client\Availability\KeyBuilder\AvailabilityResourceKeyBuilder;
use Spryker\Client\Availability\Storage\AvailabilityStorage;
use Spryker\Client\Kernel\AbstractFactory;

class AvailabilityFactory extends AbstractFactory
{

    /**
     * @param string $locale
     *
     * @return \Spryker\Client\Availability\Storage\AvailabilityStorage
     */
    public function createAvailabilityStorage($locale)
    {
        return new AvailabilityStorage(
            $this->getStorage(),
            $this->createKeyBuilder(),
            $locale
        );
    }

    /**
     * @return \Spryker\Client\Storage\StorageClientInterface
     */
    protected function getStorage()
    {
        return $this->getProvidedDependency(AvailabilityDependencyProvider::KV_STORAGE);
    }

    /**
     * @return \Spryker\Shared\Collector\Code\KeyBuilder\KeyBuilderInterface
     */
    protected function createKeyBuilder()
    {
        return new AvailabilityResourceKeyBuilder();
    }

    /**
     * @return \Spryker\Client\Locale\LocaleClient
     */
    public function getLocaleClient()
    {
        return $this->getProvidedDependency(AvailabilityDependencyProvider::CLIENT_LOCALE);
    }

}
