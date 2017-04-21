<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Service\FileSystem\Model\Builder;

interface FileSystemStorageBuilderInterface
{

    /**
     * @return \Spryker\Service\FileSystem\Model\FileSystemStorageInterface
     */
    public function build();

}
