<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Unit\Spryker\Client\ProductBundle\Plugin\Cart;

use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use PHPUnit_Framework_TestCase;
use Spryker\Client\ProductBundle\Plugin\Cart\ItemCountPlugin;

/**
 * @group Unit
 * @group Spryker
 * @group Client
 * @group ProductBundle
 * @group Plugin
 * @group Cart
 * @group ItemCountPluginTest
 */
class ItemCountPluginTest extends PHPUnit_Framework_TestCase
{

    /**
     * @return void
     */
    public function testGetItemCountReturns0WhenNoItemAndNoBundleProductInCart()
    {
        $quoteTransfer = new QuoteTransfer();
        $itemCountPlugin = new ItemCountPlugin();

        $this->assertSame(0, $itemCountPlugin->getItemCount($quoteTransfer));
    }

    /**
     * @return void
     */
    public function testGetItemCountReturns1WhenOneItemInCart()
    {
        $quoteTransfer = new QuoteTransfer();
        $quoteTransfer->addItem(new ItemTransfer());
        $itemCountPlugin = new ItemCountPlugin();

        $this->assertSame(1, $itemCountPlugin->getItemCount($quoteTransfer));
    }

    /**
     * @return void
     */
    public function testGetItemCountReturns1WhenOneBundleProductInCart()
    {
        $quoteTransfer = new QuoteTransfer();
        $quoteTransfer->addBundleItem(new ItemTransfer());
        $itemCountPlugin = new ItemCountPlugin();

        $this->assertSame(1, $itemCountPlugin->getItemCount($quoteTransfer));
    }

    /**
     * @return void
     */
    public function testGetItemCountReturns2WhenOneBundleProductAndOneItemInCart()
    {
        $quoteTransfer = new QuoteTransfer();
        $quoteTransfer->addBundleItem(new ItemTransfer());
        $quoteTransfer->addItem(new ItemTransfer());
        $itemCountPlugin = new ItemCountPlugin();

        $this->assertSame(2, $itemCountPlugin->getItemCount($quoteTransfer));
    }

}
