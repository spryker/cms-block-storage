<?php

namespace SprykerEngine\Zed\Messenger\Communication;

use SprykerEngine\Zed\Kernel\Communication\AbstractCommunicationDependencyContainer;
use SprykerEngine\Zed\Messenger\Communication\Plugin\TwigMessengerExtension;

class MessengerDependencyContainer extends AbstractCommunicationDependencyContainer
{

    /**
     * @return TwigMessengerExtension
     */
    public function createTwigMessengerExtension()
    {
        $twigMessengerExtension = new TwigMessengerExtension();

        $twigMessengerExtension->setMessenger(
            $this->getLocator()->messenger()->facade()
        );

        return $twigMessengerExtension;
    }

}
