<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\CmsBlockStorage\Business\Storage;

interface CmsBlockStorageWriterInterface
{
    public function publish(array $cmsBlockIds): void;

    public function unpublish(array $cmsBlockIds): void;
}
