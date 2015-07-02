<?php

namespace Unit\SprykerFeature\Client\ZedRequest\Service\Client;

use SprykerFeature\Shared\ZedRequest\Client\Message;

/**
 * @group Communication
 */
class MessageTest extends \PHPUnit_Framework_TestCase
{
    public function testGetterAndSetters()
    {
        $message = new Message();

        $message->setData(['test' => 'test']);
        $message->setMessage('message');

        $this->assertEquals('message', $message->getMessage());
        $this->assertEquals(['test' => 'test'], $message->getData());

        $this->assertEquals(['message' => 'message', 'data' => ['test' => 'test']], $message->toArray());
    }
}
