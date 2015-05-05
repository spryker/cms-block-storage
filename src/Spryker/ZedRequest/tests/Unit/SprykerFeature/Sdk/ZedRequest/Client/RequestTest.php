<?php

namespace Unit\SprykerFeature\Sdk\ZedRequest\Client;

use SprykerEngine\Shared\Kernel\LocatorLocatorInterface;
use SprykerEngine\Shared\Transfer\TransferInterface;
use SprykerEngine\Zed\Kernel\Locator;
use SprykerFeature\Sdk\ZedRequest\Client\Request;

/**
 * @group Communication
 */
class RequestTest extends \PHPUnit_Framework_TestCase
{
    protected function createFullRequest(
        LocatorLocatorInterface $locator,
        TransferInterface $transfer,
        TransferInterface $metaTransfer1 = null,
        TransferInterface $metaTransfer2 = null
    ) {
        $request = new Request($locator);

        $request->setPassword('password');
        $request->setHost('host');
        $request->setSessionId('sessionId');
        $request->setTime(1234567);
        $request->setTransfer($transfer);
        $request->setUsername('username');

        if ($metaTransfer1) {
            $request->addMetaTransfer('meta1', $metaTransfer1);
        }

        if ($metaTransfer2) {
            $request->addMetaTransfer('meta2', $metaTransfer2);
        }

        return $request;
    }

    public function testDefaultTransferIsNull()
    {
        $response = new Request(Locator::getInstance());
        $this->assertEquals(null, $response->getTransfer());
        $this->assertEquals(null, $response->getMetaTransfer('asd'));
    }

    public function testGetterAndSetters()
    {
        $locator = Locator::getInstance();
        $this->markTestSkipped();
        $transfer = $locator->locateSystemTestMain();
        $transfer->setBar('test');
        $request = $this->createFullRequest($locator, $transfer);

        $this->assertEquals('password', $request->getPassword());
        $this->assertEquals('host', $request->getHost());
        $this->assertEquals('sessionId', $request->getSessionId());
        $this->assertEquals(1234567, $request->getTime());
        $this->assertEquals($transfer, $request->getTransfer());
        $this->assertNotSame($transfer, $request->getTransfer());
        $this->assertNotSame($request->getTransfer(), $request->getTransfer());
        $this->assertEquals('username', $request->getUsername());
    }

    public function testMetaTransfersAreStoredCorrectly()
    {
        $this->markTestSkipped('TODO fix me after refactoring');
        $locator = Locator::getInstance();
        $transfer = new \Generated\Shared\Transfer\SystemTestMainTransfer();
        $metaTransfer1 = new \Generated\Shared\Transfer\SystemTestChildTransfer();
        $metaTransfer2 = new \Generated\Shared\Transfer\SystemTestChildTransfer();

        $metaTransfer1->setBat('1');
        $metaTransfer2->setBat('2');
        $request = $this->createFullRequest($locator, $transfer, $metaTransfer1, $metaTransfer2);

        $this->assertEquals($metaTransfer1, $request->getMetaTransfer('meta1'));
        $this->assertNotSame($metaTransfer1, $request->getMetaTransfer('meta1'));
        $this->assertEquals($metaTransfer2, $request->getMetaTransfer('meta2'));
        $this->assertNotSame($metaTransfer2, $request->getMetaTransfer('meta2'));
    }

    public function testToArrayAndFromArray()
    {
        $this->markTestSkipped();
        $locator = Locator::getInstance();
        $transfer = new \Generated\Shared\Transfer\SystemTestMainTransfer();
        $metaTransfer1 = new \Generated\Shared\Transfer\SystemTestChildTransfer();
        $metaTransfer2 = new \Generated\Shared\Transfer\SystemTestChildTransfer();
        $metaTransfer1->setBat('1');
        $metaTransfer2->setBat('2');
        $request = $this->createFullRequest($locator, $transfer, $metaTransfer1, $metaTransfer2);

        $array = $request->toArray();

        $this->assertTrue(is_array($array), 'toArray does not return array');

        $newRequest = new Request($array);

        $this->assertEquals($request, $newRequest);
        $this->assertNotSame($request, $newRequest);
    }
}
