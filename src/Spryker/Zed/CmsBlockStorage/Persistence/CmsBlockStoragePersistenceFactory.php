<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\CmsBlockStorage\Persistence;

use Orm\Zed\CmsBlock\Persistence\SpyCmsBlockQuery;
use Orm\Zed\CmsBlockStorage\Persistence\SpyCmsBlockStorageQuery;
use Spryker\Zed\Kernel\Persistence\AbstractPersistenceFactory;

/**
 * @method \Spryker\Zed\CmsBlockStorage\CmsBlockStorageConfig getConfig()
 * @method \Spryker\Zed\CmsBlockStorage\Persistence\CmsBlockStorageQueryContainerInterface getQueryContainer()
 */
class CmsBlockStoragePersistenceFactory extends AbstractPersistenceFactory
{
    public function createSpyCmsBlockStorage(): SpyCmsBlockStorageQuery
    {
        return SpyCmsBlockStorageQuery::create();
    }

    public function createCmsBlockQuery(): SpyCmsBlockQuery
    {
        return SpyCmsBlockQuery::create();
    }
}
