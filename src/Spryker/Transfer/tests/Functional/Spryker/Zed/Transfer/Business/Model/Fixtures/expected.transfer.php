<?php

/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace Generated\Shared\Transfer;

use Spryker\Shared\Transfer\AbstractTransfer;

/**
 * !!! THIS FILE IS AUTO-GENERATED, EVERY CHANGE WILL BE LOST WITH THE NEXT RUN OF TRANSFER GENERATOR
 * !!! DO NOT CHANGE ANYTHING IN THIS FILE
 */
class CatFaceTransfer extends AbstractTransfer
{

    const NAME = 'name';
    const ITEM = 'item';
    const ITEMS = 'items';

    /**
     * @var string
     */
    protected $name;

    /**
     * @var \Generated\Shared\Transfer\ItemTransfer
     */
    protected $item;

    /**
     * @var \ArrayObject|\Generated\Shared\Transfer\ItemTransfer[]
     */
    protected $items;

    /**
     * @var array
     */
    protected $transferMetadata = [
        self::NAME => [
            'type' => 'string',
            'name_underscore' => 'name',
            'is_collection' => false,
            'is_transfer' => false,
        ],
        self::ITEM => [
            'type' => 'Generated\Shared\Transfer\ItemTransfer',
            'name_underscore' => 'item',
            'is_collection' => false,
            'is_transfer' => true,
        ],
        self::ITEMS => [
            'type' => 'Generated\Shared\Transfer\ItemTransfer',
            'name_underscore' => 'items',
            'is_collection' => true,
            'is_transfer' => true,
        ],
    ];

    /**
     * @bundle Test
     *
     * @param string $name
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;
        $this->addModifiedProperty(self::NAME);

        return $this;
    }

    /**
     * @bundle Test
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @bundle Test
     *
     * @throws \Spryker\Shared\Transfer\Exception\RequiredTransferPropertyException
     *
     * @return $this
     */
    public function requireName()
    {
        $this->assertPropertyIsSet(self::NAME);

        return $this;
    }

    /**
     * @bundle Test
     *
     * @param \Generated\Shared\Transfer\ItemTransfer|null $item
     *
     * @return $this
     */
    public function setItem(ItemTransfer $item = null)
    {
        $this->item = $item;
        $this->addModifiedProperty(self::ITEM);

        return $this;
    }

    /**
     * @bundle Test
     *
     * @return \Generated\Shared\Transfer\ItemTransfer
     */
    public function getItem()
    {
        return $this->item;
    }

    /**
     * @bundle Test
     *
     * @throws \Spryker\Shared\Transfer\Exception\RequiredTransferPropertyException
     *
     * @return $this
     */
    public function requireItem()
    {
        $this->assertPropertyIsSet(self::ITEM);

        return $this;
    }

    /**
     * @bundle Test
     *
     * @param \ArrayObject|\Generated\Shared\Transfer\ItemTransfer[] $items
     *
     * @return $this
     */
    public function setItems(\ArrayObject $items)
    {
        $this->items = $items;
        $this->addModifiedProperty(self::ITEMS);

        return $this;
    }

    /**
     * @bundle Test
     *
     * @return \ArrayObject|\Generated\Shared\Transfer\ItemTransfer[]
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @bundle Test
     *
     * @param \Generated\Shared\Transfer\ItemTransfer $item
     *
     * @return $this
     */
    public function addItem(ItemTransfer $item)
    {
        $this->items[] = $item;
        $this->addModifiedProperty(self::ITEMS);

        return $this;
    }

    /**
     * @bundle Test
     *
     * @throws \Spryker\Shared\Transfer\Exception\RequiredTransferPropertyException
     *
     * @return $this
     */
    public function requireItems()
    {
        $this->assertCollectionPropertyIsSet(self::ITEMS);

        return $this;
    }

}
