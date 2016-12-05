<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Transfer\Business\Model\Generator;

use Symfony\Component\Finder\Finder;

class TransferDefinitionFinder implements FinderInterface
{

    /**
     * @deprecated Will be removed with next major release
     */
    const KEY_BUNDLE = 'bundle';

    /**
     * @deprecated Will be removed with next major release
     */
    const KEY_CONTAINING_BUNDLE = 'containing bundle';

    /**
     * @deprecated Will be removed with next major release
     */
    const KEY_TRANSFER = 'transfer';

    /**
     * @deprecated Will be removed with next major release
     */
    const TRANSFER_SCHEMA_SUFFIX = '.transfer.xml';

    /**
     * @var array
     */
    protected $sourceDirectories;

    /**
     * @param array $sourceDirectories
     */
    public function __construct(array $sourceDirectories)
    {
        $this->sourceDirectories = $sourceDirectories;
    }

    /**
     * @return \Symfony\Component\Finder\Finder|\Symfony\Component\Finder\SplFileInfo[]
     */
    public function getXmlTransferDefinitionFiles()
    {
        $finder = new Finder();
        $finder->in($this->getExistingSourceDirectories())->name('*.transfer.xml')->depth('< 1');

        return $finder;
    }

    /**
     * @return string[]
     */
    protected function getExistingSourceDirectories()
    {
        return array_filter($this->sourceDirectories, function ($directory) {
            return (bool)glob($directory, GLOB_ONLYDIR);
        });
    }

}
