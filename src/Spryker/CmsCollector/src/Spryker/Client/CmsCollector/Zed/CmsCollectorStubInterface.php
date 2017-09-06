<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Client\CmsCollector\Zed;

use Generated\Shared\Transfer\CmsPageCollectorDataTransfer;

interface CmsCollectorStubInterface
{

    /**
     * @param \Generated\Shared\Transfer\CmsPageCollectorDataTransfer $cmsPageCollectorDataTransfer
     *
     * @return \Generated\Shared\Transfer\CmsPageCollectorDataTransfer
     */
    public function expandCmsPageCollectorData(CmsPageCollectorDataTransfer $cmsPageCollectorDataTransfer);

}
