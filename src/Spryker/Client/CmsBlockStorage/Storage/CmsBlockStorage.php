<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Client\CmsBlockStorage\Storage;

use Generated\Shared\Transfer\CmsBlockRequestTransfer;
use Generated\Shared\Transfer\SynchronizationDataTransfer;
use Spryker\Client\CmsBlockStorage\Dependency\Client\CmsBlockStorageToStorageInterface;
use Spryker\Client\CmsBlockStorage\Dependency\Service\CmsBlockStorageToSynchronizationServiceInterface;
use Spryker\Client\CmsBlockStorage\Dependency\Service\CmsBlockStorageToUtilEncodingServiceInterface;
use Spryker\Shared\CmsBlockStorage\CmsBlockStorageConstants;

class CmsBlockStorage implements CmsBlockStorageInterface
{
    protected const OPTION_NAME = 'name';
    protected const OPTION_KEYS = 'keys';

    protected const PREFIX_MAPPING_CMS_BLOCK_KEY = 'name:';

    /**
     * @var \Spryker\Client\CmsBlockStorage\Dependency\Client\CmsBlockStorageToStorageInterface
     */
    protected $storageClient;

    /**
     * @var \Spryker\Client\CmsBlockStorage\Dependency\Service\CmsBlockStorageToSynchronizationServiceInterface
     */
    protected $synchronizationService;

    /**
     * @var \Spryker\Client\CmsBlockStorage\Dependency\Service\CmsBlockStorageToUtilEncodingServiceInterface
     */
    protected $utilEncodingService;

    /**
     * @var \Spryker\Client\CmsBlockStorageExtension\Dependency\Plugin\CmsBlockStorageReaderPluginInterface[]
     */
    protected $cmsBlockStorageBlocksFinderPlugins;

    /**
     * @param \Spryker\Client\CmsBlockStorage\Dependency\Client\CmsBlockStorageToStorageInterface $storageClient
     * @param \Spryker\Client\CmsBlockStorage\Dependency\Service\CmsBlockStorageToSynchronizationServiceInterface $synchronizationService
     * @param \Spryker\Client\CmsBlockStorage\Dependency\Service\CmsBlockStorageToUtilEncodingServiceInterface $utilEncodingService
     * @param \Spryker\Client\CmsBlockStorageExtension\Dependency\Plugin\CmsBlockStorageReaderPluginInterface[] $cmsBlockStorageBlocksFinderPlugins
     */
    public function __construct(
        CmsBlockStorageToStorageInterface $storageClient,
        CmsBlockStorageToSynchronizationServiceInterface $synchronizationService,
        CmsBlockStorageToUtilEncodingServiceInterface $utilEncodingService,
        array $cmsBlockStorageBlocksFinderPlugins
    ) {
        $this->storageClient = $storageClient;
        $this->synchronizationService = $synchronizationService;
        $this->utilEncodingService = $utilEncodingService;
        $this->cmsBlockStorageBlocksFinderPlugins = $cmsBlockStorageBlocksFinderPlugins;
    }

    /**
     * @param array $options
     * @param string $localeName
     * @param string $storeName
     *
     * @return array[]
     */
    public function getCmsBlocksByOptions(array $options, string $localeName, string $storeName): array
    {
        $cmsBlockKeys = $options[static::OPTION_KEYS] ?? [];

        if ($cmsBlockKeys) {
            return $this->getBlocksByKeys($cmsBlockKeys, $localeName, $storeName);
        }

        $cmsBlockName = $options[static::OPTION_NAME] ?? null;

        if ($cmsBlockName) {
            $cmsBlockData = $this->findBlockByName($cmsBlockName, $localeName, $storeName);

            return $cmsBlockData ? [$cmsBlockData] : [];
        }

        $availableBlockKeys = $this->getBlockKeysByOptions($options);

        return $this->getBlocksByKeys($availableBlockKeys, $localeName, $storeName);
    }

    /**
     * @param string[] $blockKeys
     * @param string $localeName
     * @param string $storeName
     *
     * @return array
     */
    protected function getBlocksByKeys(array $blockKeys, string $localeName, string $storeName): array
    {
        $storageKeys = [];

        foreach ($blockKeys as $blockKey) {
            $storageKeys[] = $this->generateKey(
                $blockKey,
                CmsBlockStorageConstants::CMS_BLOCK_RESOURCE_NAME,
                $localeName,
                $storeName
            );
        }

        $resultArray = $this->storageClient->getMulti($storageKeys) ?: [];
        $resultArray = array_filter($resultArray);

        $blocks = [];

        foreach ($resultArray as $key => $result) {
            $blocks[] = $this->utilEncodingService->decodeJson($result, true);
        }

        return $blocks;
    }

    /**
     * @param string $blockName
     * @param string $localeName
     * @param string $storeName
     *
     * @return array|null
     */
    protected function findBlockByName(string $blockName, $localeName, $storeName): ?array
    {
        $blockKey = $this->findBlockKeyByBlockName($blockName, $localeName, $storeName);

        if (!$blockKey) {
            return null;
        }

        $storageKey = $this->generateKey($blockKey, CmsBlockStorageConstants::CMS_BLOCK_RESOURCE_NAME, $localeName, $storeName);
        $cmsBlockStorageData = $this->storageClient->get($storageKey);
        if (!$cmsBlockStorageData) {
            return null;
        }

        return $cmsBlockStorageData;
    }

    /**
     * @param array $options
     *
     * @return string[]
     */
    protected function getBlockKeysByOptions(array $options): array
    {
        $blockKeys = [];

        $cmsBlockRequestTransfer = $this->mapOptionsToTransfer($options);

        foreach ($this->cmsBlockStorageBlocksFinderPlugins as $cmsBlockStorageBlocksFinderPlugin) {
            $cmsBlockTransfers = $cmsBlockStorageBlocksFinderPlugin->getCmsBlocks($cmsBlockRequestTransfer);

            if (count($cmsBlockTransfers) < 1) {
                continue;
            }

            $blockKeys = array_merge($blockKeys, $this->getBlockKeysFromTransfers($cmsBlockTransfers));
        }

        return $blockKeys;
    }

    /**
     * @param string $blockName
     * @param string $localeName
     * @param string $storeName
     *
     * @return string|null
     */
    protected function findBlockKeyByBlockName(string $blockName, string $localeName, string $storeName): ?string
    {
        $blockNameKey = static::PREFIX_MAPPING_CMS_BLOCK_KEY . $blockName;
        $searchKey = $this->generateKey($blockNameKey, CmsBlockStorageConstants::CMS_BLOCK_RESOURCE_NAME, $localeName, $storeName);

        $mappingData = $this->storageClient->get($searchKey);
        if (!$mappingData) {
            return null;
        }

        return $mappingData['id'];
    }

    /**
     * @param \Generated\Shared\Transfer\CmsBlockTransfer[] $cmsBlockTransfers
     *
     * @return string[]
     */
    protected function getBlockKeysFromTransfers(array $cmsBlockTransfers): array
    {
        $blockKeys = [];

        foreach ($cmsBlockTransfers as $cmsBlockTransfer) {
            $blockKeys[] = $cmsBlockTransfer->getKey();
        }

        return $blockKeys;
    }

    /**
     * @param string $blockKey
     * @param string $resourceName
     * @param string $localeName
     * @param string $storeName
     *
     * @return string
     */
    protected function generateKey(string $blockKey, string $resourceName, string $localeName, string $storeName): string
    {
        $synchronizationDataTransfer = new SynchronizationDataTransfer();
        $synchronizationDataTransfer->setStore($storeName);
        $synchronizationDataTransfer->setLocale($localeName);
        $synchronizationDataTransfer->setReference($blockKey);

        return $this->synchronizationService->getStorageKeyBuilder($resourceName)->generateKey($synchronizationDataTransfer);
    }

    /**
     * @param array $options
     *
     * @return \Generated\Shared\Transfer\CmsBlockRequestTransfer
     */
    protected function mapOptionsToTransfer(array $options): CmsBlockRequestTransfer
    {
        return (new CmsBlockRequestTransfer())->fromArray($options);
    }
}
