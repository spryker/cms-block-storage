<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Client\CmsBlockStorage;

use Spryker\Client\CmsBlockStorage\Dependency\Client\CmsBlockStorageToStorageInterface;
use Spryker\Client\CmsBlockStorage\Dependency\Service\CmsBlockStorageToSynchronizationServiceInterface;
use Spryker\Client\CmsBlockStorage\Dependency\Service\CmsBlockStorageToUtilEncodingServiceInterface;
use Spryker\Client\CmsBlockStorage\Storage\CmsBlockStorage;
use Spryker\Client\CmsBlockStorage\Storage\CmsBlockStorageInterface;
use Spryker\Client\Kernel\AbstractFactory;

class CmsBlockStorageFactory extends AbstractFactory
{
    public function createCmsBlockStorage(): CmsBlockStorageInterface
    {
        return new CmsBlockStorage(
            $this->getStorage(),
            $this->getSynchronizationService(),
            $this->getUtilEncodingService(),
            $this->getCmsBlockStorageReaderPlugins(),
        );
    }

    /**
     * @return array<\Spryker\Client\CmsBlockStorageExtension\Dependency\Plugin\CmsBlockStorageReaderPluginInterface>
     */
    public function getCmsBlockStorageReaderPlugins(): array
    {
        return $this->getProvidedDependency(CmsBlockStorageDependencyProvider::PLUGINS_CMS_BLOCK_STORAGE_READER);
    }

    public function getStorage(): CmsBlockStorageToStorageInterface
    {
        return $this->getProvidedDependency(CmsBlockStorageDependencyProvider::CLIENT_STORAGE);
    }

    public function getSynchronizationService(): CmsBlockStorageToSynchronizationServiceInterface
    {
        return $this->getProvidedDependency(CmsBlockStorageDependencyProvider::SERVICE_SYNCHRONIZATION);
    }

    protected function getUtilEncodingService(): CmsBlockStorageToUtilEncodingServiceInterface
    {
        return $this->getProvidedDependency(CmsBlockStorageDependencyProvider::SERVICE_UTIL_ENCODING);
    }
}
