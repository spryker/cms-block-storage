<?php

namespace SprykerEngine\Shared\Messenger\Business\Model;

use SprykerEngine\Shared\Messenger\Business\Model\Exception\MessageTypeNotFoundException;
use SprykerEngine\Shared\Messenger\Business\Model\Message\MessageInterface;
use SprykerEngine\Shared\Messenger\Communication\Presenter\ObservingPresenterInterface;

/**
 * @method Messenger addSuccess($key, $options = [])
 * @method Messenger addError($key, $options = [])
 * @method Messenger addNotice($key, $options = [])
 * @method Messenger addWarning($key, $options = [])
 */
interface MessengerInterface
{

    /**
     * @param string $type
     * @param string $message
     * @param array $options
     *
     * @return Messenger
     * @throws MessageTypeNotFoundException
     */
    public function add($type, $message, array $options = []);

    /**
     * @param string $type
     *
     * @return MessageInterface
     */
    public function get($type = null);

    /**
     * @param string $type
     *
     * @return MessageInterface[]
     */
    public function getAll($type = null);

    /**
     * @param ObservingPresenterInterface $presenter
     *
     * @return MessengerInterface
     */
    public function registerPresenter(ObservingPresenterInterface $presenter);

}
