<?php

/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace Spryker\Zed\SequenceNumber\Business;

use Spryker\Zed\SequenceNumber\Business\Model\SequenceNumber;
use Spryker\Zed\SequenceNumber\Business\Generator\RandomNumberGenerator;
use Generated\Shared\Transfer\SequenceNumberSettingsTransfer;
use Spryker\Zed\Kernel\Business\AbstractBusinessFactory;
use Propel\Runtime\Propel;

/**
 * @method \Spryker\Zed\SequenceNumber\SequenceNumberConfig getConfig()
 * @method \Spryker\Zed\SequenceNumber\Persistence\SequenceNumberQueryContainerInterface getQueryContainer()
 */
class SequenceNumberBusinessFactory extends AbstractBusinessFactory
{

    /**
     * @param int $min
     * @param int $max
     *
     * @return \Spryker\Zed\SequenceNumber\Business\Generator\RandomNumberGeneratorInterface
     */
    public function createRandomNumberGenerator($min = 1, $max = 1)
    {
        return new RandomNumberGenerator(
                $min,
                $max
            );
    }

    /**
     * @param \Generated\Shared\Transfer\SequenceNumberSettingsTransfer $sequenceNumberSettings
     *
     * @return \Spryker\Zed\SequenceNumber\Business\Model\SequenceNumberInterface
     */
    public function createSequenceNumber(SequenceNumberSettingsTransfer $sequenceNumberSettings)
    {
        $settings = $this->getConfig()->getDefaultSettings($sequenceNumberSettings);

        $generator = $this->createRandomNumberGenerator($settings->getIncrementMinimum(), $settings->getIncrementMaximum());

        return new SequenceNumber(
                $generator,
                $settings,
                Propel::getConnection()
            );
    }

}
