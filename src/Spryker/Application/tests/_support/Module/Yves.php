<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Application\Module;

use Codeception\Module;
use Spryker\Shared\Application\ApplicationConstants;
use Spryker\Shared\Config\Config;

class Yves extends Module
{

    /**
     * @return $this
     */
    public function amYves()
    {
        $url = 'http://' . Config::get(ApplicationConstants::HOST_YVES);

        $this->getModule('WebDriver')->_reconfigure(['url' => $url]);

        return $this;
    }

}
