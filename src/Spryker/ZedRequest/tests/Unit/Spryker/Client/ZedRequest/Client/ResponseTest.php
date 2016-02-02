<?php

/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace Unit\Spryker\Client\ZedRequest\Client;

use Spryker\Shared\ZedRequest\Client\Message;
use Spryker\Client\ZedRequest\Client\Response;
use Spryker\Shared\Transfer\TransferInterface;
use Unit\Spryker\Client\ZedRequest\Client\Fixture\TestTransfer;

/**
 * @group Spryker
 * @group Client
 * @group ZedRequest
 */
class ResponseTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @param TransferInterface $transfer
     *
     * @return \Spryker\Client\ZedRequest\Client\Response
     */
    protected function createFullResponse(TransferInterface $transfer)
    {
        $response = new Response();

        $response->setSuccess(false);
        $response->addErrorMessages([new Message(['message' => 'error'])]);
        $response->addInfoMessages([new Message(['message' => 'info'])]);
        $response->addSuccessMessages([new Message(['message' => 'success'])]);
        $response->setTransfer($transfer);

        return $response;
    }

    /**
     * @return void
     */
    public function testDefaultSuccessIsTrue()
    {
        $response = new Response();
        $this->assertEquals(true, $response->isSuccess());
    }

    /**
     * @return void
     */
    public function testDefaultTransferIsNull()
    {
        $response = new Response();
        $this->assertEquals(null, $response->getTransfer());
    }

    /**
     * @return void
     */
    public function testGetterAndSetters()
    {
        $transfer = new TestTransfer();
        $transfer->setFoo('foo');

        $response = $this->createFullResponse($transfer);

        $this->assertEquals(false, $response->isSuccess());
        $this->assertEquals([new Message(['message' => 'error'])], $response->getErrorMessages());
        $this->assertEquals([new Message(['message' => 'info'])], $response->getInfoMessages());
        $this->assertEquals([new Message(['message' => 'success'])], $response->getSuccessMessages());
        $this->assertEquals($transfer, $response->getTransfer());
        $this->assertNotSame($transfer, $response->getTransfer());
        $this->assertNotSame($response->getTransfer(), $response->getTransfer());
    }

    /**
     * @return void
     */
    public function testToArrayAndFromArray()
    {
        $transfer = new TestTransfer();
        $transfer->setFoo('foo');

        $response = $this->createFullResponse($transfer);

        $array = $response->toArray();
        $this->assertTrue(is_array($array), 'toArray does not return array');

        $newResponse = new Response($array);

        $this->assertEquals($response, $newResponse);
        $this->assertNotSame($response, $newResponse);
    }

    /**
     * @return void
     */
    public function testHasMethods()
    {
        $response = new Response();

        $response->addErrorMessage(new Message(['message' => 'error']));
        $response->addInfoMessage(new Message(['message' => 'test']));

        $this->assertEquals(true, $response->hasErrorMessage('error'));
        $this->assertEquals(false, $response->hasErrorMessage('test'));
        $this->assertEquals(false, $response->hasInfoMessage('error'));
        $this->assertEquals(true, $response->hasInfoMessage('test'));
    }

}
