<?php

/**
 * (c) Spryker Systems GmbH copyright protected.
 */

namespace SprykerFeature\Client\Cms;

use Generated\Shared\Transfer\CmsBlockTransfer;

interface CmsClientInterface
{

    /**
     * @param CmsBlockTransfer $cmsBlockTransfer
     *
     * @return array
     */
    public function findBlockByName(CmsBlockTransfer $cmsBlockTransfer);

}
