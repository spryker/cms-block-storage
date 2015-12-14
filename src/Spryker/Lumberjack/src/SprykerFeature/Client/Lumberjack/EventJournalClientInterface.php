<?php
/**
 * (c) Copyright Spryker Systems GmbH 2015
 */
namespace SprykerFeature\Client\Lumberjack;

use SprykerEngine\Shared\Lumberjack\Model\EventInterface;

interface EventJournalClientInterface
{

    /**
     * @param EventInterface $event
     *
     * @return void
     */
    public function saveEvent(EventInterface $event);

}
