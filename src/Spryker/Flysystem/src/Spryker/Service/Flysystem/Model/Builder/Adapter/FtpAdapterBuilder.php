<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Service\Flysystem\Model\Builder\Adapter;

use Generated\Shared\Transfer\FlysystemConfigFtpTransfer;
use Generated\Shared\Transfer\FlysystemConfigTransfer;
use League\Flysystem\Adapter\Ftp as FtpAdapter;

class FtpAdapterBuilder implements AdapterBuilderInterface
{

    /**
     * @var \League\Flysystem\Adapter\Ftp
     */
    protected $adapter;

    /**
     * @var \Generated\Shared\Transfer\FlysystemConfigFtpTransfer
     */
    protected $adapterConfig;

    /**
     * @var \Generated\Shared\Transfer\FlysystemConfigTransfer
     */
    protected $fileSystemConfig;

    /**
     * @param \Generated\Shared\Transfer\FlysystemConfigTransfer $fileSystemConfig
     * @param \Generated\Shared\Transfer\FlysystemConfigFtpTransfer $adapterConfig
     */
    public function __construct(
        FlysystemConfigTransfer $fileSystemConfig,
        FlysystemConfigFtpTransfer $adapterConfig
    ) {
        $this->fileSystemConfig = $fileSystemConfig;
        $this->adapterConfig = $adapterConfig;
    }

    /**
     * @return \League\Flysystem\AdapterInterface
     */
    public function build()
    {
        $this
            ->buildAdapter();

        return $this->adapter;
    }

    /**
     * @return $this
     */
    protected function buildAdapter()
    {
        $this->adapter = new FtpAdapter($this->adapterConfig->modifiedToArray());

        return $this;
    }

}
