<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Client\CmsBlockStorage;

interface CmsBlockStorageClientInterface
{
    /**
     * Specification:
     * - Find blocks by provided array of names with a single multi request to a storage
     *
     * @api
     *
     * @param array<string> $blockNames
     * @param string $localeName
     * @param string $storeName
     *
     * @return array
     */
    public function findBlocksByNames($blockNames, $localeName, $storeName): array;

    /**
     * Specification:
     * - Finds blocks by the provided array of options with a single multi request to a storage.
     *
     * @api
     *
     * @param array<string, mixed> $options
     * @param string $localeName
     * @param string $storeName
     *
     * @return array
     */
    public function getCmsBlocksByOptions(array $options, string $localeName, string $storeName): array;
}
