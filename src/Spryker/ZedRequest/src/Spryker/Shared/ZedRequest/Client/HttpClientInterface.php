<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Shared\ZedRequest\Client;

use Spryker\Shared\Transfer\TransferInterface;

/**
 * Interface HttpClientInterface
 */
interface HttpClientInterface
{

    /**
     * @param int $timeoutInSeconds
     *
     * @return void
     */
    public static function setDefaultTimeout($timeoutInSeconds);

    /**
     * @param string $pathInfo
     * @param \Spryker\Shared\Transfer\TransferInterface|null $transferObject
     * @param array $metaTransfers
     * @param int|null $timeoutInSeconds
     *
     * @throws \LogicException
     *
     * @return \Spryker\Shared\Library\Communication\Response
     */
    public function request(
        $pathInfo,
        TransferInterface $transferObject = null,
        array $metaTransfers = [],
        $timeoutInSeconds = null
    );

    /**
     * Used for debug output
     *
     * @return int
     */
    public static function getRequestCounter();

}
