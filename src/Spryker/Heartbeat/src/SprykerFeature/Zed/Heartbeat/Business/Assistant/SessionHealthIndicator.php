<?php

/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace SprykerFeature\Zed\Heartbeat\Business\Assistant;

use SprykerFeature\Shared\Heartbeat\Code\AbstractHealthIndicator;
use SprykerFeature\Shared\Heartbeat\Code\HealthIndicatorInterface;

class SessionHealthIndicator extends AbstractHealthIndicator implements HealthIndicatorInterface
{

    const HEALTH_MESSAGE_UNABLE_TO_WRITE_SESSION = 'Unable to write session';
    const HEALTH_MESSAGE_UNABLE_TO_READ_SESSION = 'Unable to read session';
    const KEY_HEARTBEAT = 'heartbeat';

    /**
     * @return void
     */
    public function healthCheck()
    {
        $this->checkWriteSession();
        $this->checkReadSession();
    }

    /**
     * @return void
     */
    private function checkWriteSession()
    {
        try {
            $_SESSION[self::KEY_HEARTBEAT] = 'ok';
        } catch (\Exception $e) {
            $this->addDysfunction(self::HEALTH_MESSAGE_UNABLE_TO_WRITE_SESSION);
            $this->addDysfunction($e->getMessage());
        }
    }

    /**
     * @return void
     */
    private function checkReadSession()
    {
        try {
            $status = $_SESSION[self::KEY_HEARTBEAT];
        } catch (\Exception $e) {
            $this->addDysfunction(self::HEALTH_MESSAGE_UNABLE_TO_READ_SESSION);
            $this->addDysfunction($e->getMessage());
        }
    }

}
