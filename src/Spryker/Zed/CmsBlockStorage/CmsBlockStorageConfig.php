<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\CmsBlockStorage;

use Spryker\Zed\Kernel\AbstractBundleConfig;

class CmsBlockStorageConfig extends AbstractBundleConfig
{
    /**
     * @api
     *
     * @deprecated Use {@link \Spryker\Zed\SynchronizationBehavior\SynchronizationBehaviorConfig::isSynchronizationEnabled()} instead.
     *
     * @return bool
     */
    public function isSendingToQueue(): bool
    {
        return true;
    }

    /**
     * @api
     *
     * @return string|null
     */
    public function getCmsBlockSynchronizationPoolName(): ?string
    {
        return null;
    }

    /**
     * @api
     *
     * @return string|null
     */
    public function getEventQueueName(): ?string
    {
        return null;
    }
}
