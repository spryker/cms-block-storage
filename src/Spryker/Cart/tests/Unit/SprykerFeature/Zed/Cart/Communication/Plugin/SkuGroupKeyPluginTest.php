<?php

/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace Unit\SprykerFeature\Zed\Cart\Communication\Plugin;

use Generated\Shared\Transfer\ChangeTransfer;
use Generated\Shared\Transfer\ItemTransfer;
use SprykerEngine\Zed\Kernel\Communication\Factory;
use SprykerEngine\Zed\Kernel\Locator;
use SprykerFeature\Zed\Cart\Communication\Plugin\SkuGroupKeyPlugin;

/**
 * @group SprykerFeature
 * @group Zed
 * @group Cart
 * @group Communication
 * @group SkuGroupKeyPlugin
 */
class SkuGroupKeyPluginTest extends \PHPUnit_Framework_TestCase
{

    const SKU = 'sku';

    /**
     * @return void
     */
    public function testExpandItemMustSetGroupKeyToSkuOfGivenProductWhenNoGroupKeyIsSet()
    {
        $itemTransfer = new ItemTransfer();
        $itemTransfer->setSku(self::SKU);

        $changeTransfer = new ChangeTransfer();
        $changeTransfer->addItem($itemTransfer);

        $plugin = new SkuGroupKeyPlugin(new Factory('Cart'), Locator::getInstance());
        $plugin->expandItems($changeTransfer);

        $this->assertSame(self::SKU, $changeTransfer->getItems()[0]->getGroupKey());
    }

    /**
     * @return void
     */
    public function testExpandItemMustNotChangeGroupKeyWhenGroupKeyIsSet()
    {
        $itemTransfer = new ItemTransfer();
        $itemTransfer->setSku(self::SKU);
        $itemTransfer->setGroupKey(self::SKU);

        $changeTransfer = new ChangeTransfer();
        $changeTransfer->addItem($itemTransfer);

        $plugin = new SkuGroupKeyPlugin(new Factory('Cart'), Locator::getInstance());
        $plugin->expandItems($changeTransfer);

        $this->assertSame(self::SKU, $changeTransfer->getItems()[0]->getGroupKey());
    }

}
