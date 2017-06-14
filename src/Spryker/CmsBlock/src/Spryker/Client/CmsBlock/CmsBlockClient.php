<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Client\CmsBlock;

use Generated\Shared\Transfer\CmsBlockTransfer;
use Spryker\Client\Kernel\AbstractClient;

/**
 * @method CmsBlockFactory getFactory()
 */
class CmsBlockClient extends AbstractClient implements CmsBlockClientInterface
{

    /**
     * @api
     *
     * @param \Generated\Shared\Transfer\CmsBlockTransfer $cmsBlockTransfer
     * @param string $localeName
     *
     * @return array
     */
    public function findBlockByName(CmsBlockTransfer $cmsBlockTransfer, $localeName)
    {
        return $this->getFactory()
            ->createCmsBlockFinder()
            ->getBlockByName($cmsBlockTransfer, $localeName);
    }

}
