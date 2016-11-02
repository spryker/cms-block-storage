<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Client\ZedRequest;

use Spryker\Client\Kernel\AbstractClient;
use Spryker\Shared\Transfer\TransferInterface;

/**
 * @method \Spryker\Client\ZedRequest\ZedRequestFactory getFactory()
 */
class ZedRequestClient extends AbstractClient implements ZedRequestClientInterface
{

    /**
     * @var \Spryker\Client\ZedRequest\Client\ZedClient
     */
    private $zedClient;

    /**
     * @return \Spryker\Client\ZedRequest\Client\ZedClient
     */
    private function getClient()
    {
        if ($this->zedClient === null) {
            $this->zedClient = $this->getFactory()->createClient();
        }

        return $this->zedClient;
    }

    /**
     * @api
     *
     * @param string $url
     * @param \Spryker\Shared\Transfer\TransferInterface $object
     * @param int|null $timeoutInSeconds
     *
     * @return \Spryker\Shared\Transfer\TransferInterface
     */
    public function call($url, TransferInterface $object, $timeoutInSeconds = null)
    {
        return $this->getClient()->call($url, $object, $timeoutInSeconds);
    }

    /**
     * @api
     *
     * @return \Spryker\Shared\ZedRequest\Client\Message[]
     */
    public function getLastResponseInfoMessages()
    {
        if (!$this->getClient()->hasLastResponse()) {
            return [];
        }

        return $this->getClient()->getLastResponse()->getInfoMessages();
    }

    /**
     * @api
     *
     * @return \Spryker\Shared\ZedRequest\Client\Message[]
     */
    public function getLastResponseErrorMessages()
    {
        if (!$this->getClient()->hasLastResponse()) {
            return [];
        }

        return $this->getClient()->getLastResponse()->getErrorMessages();
    }

    /**
     * @api
     *
     * @return \Spryker\Shared\ZedRequest\Client\Message[]
     */
    public function getLastResponseSuccessMessages()
    {
        if (!$this->getClient()->hasLastResponse()) {
            return [];
        }

        return $this->getClient()->getLastResponse()->getSuccessMessages();
    }

}
