<?php

/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace Generated\Shared\Test;

use Generated\Shared\Transfer\ItemTransfer;
use SprykerEngine\Shared\Transfer\TransferInterface;

/**
 * !!! THIS FILE IS AUTO-GENERATED, EVERY CHANGE WILL BE LOST WITH THE NEXT RUN OF TRANSFER GENERATOR
 * !!! DO NOT CHANGE ANYTHING IN THIS FILE
 */
interface CatFaceInterface extends TransferInterface
{

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName($name);

    /**
     * @return string
     */
    public function getName();

    /**
     * @param ItemTransfer $item
     *
     * @return $this
     */
    public function setItem(ItemTransfer $item);

    /**
     * @return ItemTransfer
     */
    public function getItem();

    /**
     * @param \ArrayObject $items
     *
     * @return $this
     */
    public function setItems(\ArrayObject $items);

    /**
     * @return ItemTransfer[]
     */
    public function getItems();

    /**
     * @param ItemTransfer $item
     *
     * @return $this
     */
    public function addItem(ItemTransfer $item);

}
