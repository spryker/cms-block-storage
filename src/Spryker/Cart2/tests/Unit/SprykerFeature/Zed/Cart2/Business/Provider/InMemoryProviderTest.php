<?php

namespace Unit\SprykerFeature\Zed\Cart2\Business\Provider;

use SprykerEngine\Shared\Transfer\AbstractTransfer;
use SprykerEngine\Zed\Kernel\Locator;
use Generated\Shared\Transfer\CartTransfer;
use Generated\Shared\Transfer\Cart2ItemTransfer;
use SprykerFeature\Zed\Cart2\Business\StorageProvider\InMemoryProvider;
use SprykerFeature\Zed\Cart2\Business\StorageProvider\StorageProviderInterface;

class InMemoryProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var StorageProviderInterface
     */
    private $provider;

    protected function setUp()
    {
        $this->markTestSkipped();
        parent::setUp();
        $this->provider = new InMemoryProvider();
    }

    //@todo test with more then 1 item

    /**
     * @group Cart
     */
    public function testAddExistingItem()
    {
        $cart = $this->createCartWithItem($itemId = '123', $existingQuantity = 1);

        $newItem = $this->createItem($itemId, $newQuantity = 3);
        $newItems = new ItemCollection(Locator::getInstance());
        $newItems->add($newItem);

        $changedCart = $this->provider->addItems($cart, $newItems);
        $changedItems = $changedCart->getItems();
        $this->assertCount(1, $changedItems);

        /** @var Item $changedItem */
        $changedItem = $changedItems->getFirstItem();
        $this->assertEquals($itemId, $changedItem->getId());
        $this->assertEquals(
            $existingQuantity + $newQuantity,
            $changedItem->getQuantity()
        );
    }

    /**
     * @group Cart
     */
    public function testAddNewItem()
    {
        $cart = $this->createCartWithItem($itemId = '123', $existingQuantity = 1);

        $newItem = $this->createItem($newId = '321', $newQuantity = 3);
        $newItems = new ItemCollection(Locator::getInstance());
        $newItems->add($newItem);

        $changedCart = $this->provider->addItems($cart, $newItems);
        $changedItems = $changedCart->getItems();
        $this->assertCount(2, $changedItems);

        $this->assertTrue($changedItems->offsetExists($itemId));
        $this->assertTrue($changedItems->offsetExists($newId));

        /** @var ItemInterface $addedItem */
        $addedItem = $changedItems->offsetGet($newId);
        $this->assertEquals($newId, $addedItem->getId());
        $this->assertEquals($newQuantity, $addedItem->getQuantity());

        /** @var ItemInterface $existingItem */
        $existingItem = $changedItems->offsetGet($itemId);
        $this->assertEquals($itemId, $existingItem->getId());
        $this->assertEquals($existingQuantity, $existingItem->getQuantity());
    }

    /**
     * @group Cart
     */
    public function testRemoveExistingItem()
    {
        $cart = $this->createCartWithItem($itemId = '123', $existingQuantity = 1);
        $newItem = $this->createItem($itemId, $reduceQuantity = 1);
        $newItems = new ItemCollection(Locator::getInstance());
        $newItems->add($newItem);

        $changedCart = $this->provider->removeItems($cart, $newItems);
        $this->assertCount(0, $changedCart->getItems());
    }

    /**
     * @group Cart
     */
    public function testRemoveNotExistingItem()
    {
        $cart = $this->createCartWithItem($itemId = '123', $existingQuantity = 1);
        $newItem = $this->createItem($deleteItemId = '321', $reduceQuantity = 1);
        $newItems = new ItemCollection(Locator::getInstance());
        $newItems->add($newItem);

        $changedCart = $this->provider->removeItems($cart, $newItems);
        $changedItems = $changedCart->getItems();
        $this->assertCount(1, $changedItems);
        /** @var ItemInterface $item */
        $item = $changedItems->getFirstItem();
        $this->assertEquals($itemId, $item->getId());
        $this->assertEquals($existingQuantity, $item->getQuantity());
    }

    /**
     * @group Cart
     */
    public function testReduceWithMoreThenExists()
    {
        $cart = $this->createCartWithItem($itemId = '123', $existingQuantity = 1);
        $newItem = $this->createItem($itemId, $reduceQuantity = 3);
        $newItems = new ItemCollection(Locator::getInstance());
        $newItems->add($newItem);

        $changedCart = $this->provider->removeItems($cart, $newItems);
        $this->assertCount(0, $changedCart->getItems());
    }

    /**
     * @group Cart
     * @expectedException \SprykerFeature\Zed\Cart2\Business\Exception\InvalidArgumentException
     * @expectedExceptionMessage Could not increase cart item 123 with -3 as value
     */
    public function testIncreaseWithNegativeValue()
    {
        $cart = $this->createCartWithItem($itemId = '123', $existingQuantity = 1);

        $newItem = $this->createItem($newId = '123', $newQuantity = -3);
        $newItems = new ItemCollection(Locator::getInstance());
        $newItems->add($newItem);

        $this->provider->addItems($cart, $newItems);
    }

    /**
     * @group Cart
     * @expectedException \SprykerFeature\Zed\Cart2\Business\Exception\InvalidArgumentException
     * @expectedExceptionMessage Could not increase cart item 123 with 0 as value
     */
    public function testIncreaseWithZeroValue()
    {
        $cart = $this->createCartWithItem($itemId = '123', $existingQuantity = 1);

        $newItem = $this->createItem($newId = '123', $newQuantity = 0);
        $newItems = new ItemCollection(Locator::getInstance());
        $newItems->add($newItem);

        $this->provider->addItems($cart, $newItems);
    }

    /**
     * @group Cart
     * @expectedException \SprykerFeature\Zed\Cart2\Business\Exception\InvalidArgumentException
     * @expectedExceptionMessage Could not decrease cart item 123 with -3 as value
     */
    public function testDecreaseWithNegativeValue()
    {
        $cart = $this->createCartWithItem($itemId = '123', $existingQuantity = 1);

        $newItem = $this->createItem($newId = '123', $newQuantity = -3);
        $newItems = new ItemCollection(Locator::getInstance());
        $newItems->add($newItem);

        $this->provider->removeItems($cart, $newItems);
    }

    /**
     * @group Cart
     * @expectedException \SprykerFeature\Zed\Cart2\Business\Exception\InvalidArgumentException
     * @expectedExceptionMessage Could not decrease cart item 123 with 0 as value
     */
    public function testDecreaseWithZeroValue()
    {
        $cart = $this->createCartWithItem($itemId = '123', $existingQuantity = 1);

        $newItem = $this->createItem($newId = '123', $newQuantity = 0);
        $newItems = new ItemCollection(Locator::getInstance());
        $newItems->add($newItem);

        $this->provider->removeItems($cart, $newItems);
    }


    /**
     *
     * @param string $itemId
     * @param int $itemQuantity
     *
     * @return CartInterface
     */
    protected function createCartWithItem($itemId, $itemQuantity)
    {
        $locator = Locator::getInstance();
        $cart = new Cart($locator);
        $existingItem = $this->createItem($itemId, $itemQuantity);
        $existingItems = new ItemCollection($locator);
        $existingItems->add($existingItem);
        $cart->setItems($existingItems);

        return $cart;
    }

    /**
     * @param string $itemId
     * @param int $itemQuantity
     *
     * @return ItemInterface|AbstractTransfer
     */
    protected function createItem($itemId, $itemQuantity)
    {
        $existingItem = new Item(Locator::getInstance());
        $existingItem->setQuantity($itemQuantity);
        $existingItem->setId($itemId);

        return $existingItem;
    }
}
