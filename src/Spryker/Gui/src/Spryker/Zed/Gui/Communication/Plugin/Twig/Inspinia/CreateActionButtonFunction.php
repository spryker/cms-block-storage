<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Gui\Communication\Plugin\Twig\Inspinia;

class CreateActionButtonFunction extends AbstractActionButtonFunction
{

    /**
     * @return string
     */
    protected function getButtonClass()
    {
        return 'btn-success';
    }

    /**
     * @return string
     */
    protected function getIcon()
    {
        return '<i class="fa fa-plus"></i> ';
    }

    /**
     * @return string
     */
    protected function getFunctionName()
    {
        return 'createActionButton';
    }

}
