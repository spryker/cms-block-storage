<?php
/**
 * Copyright © 2017-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\CmsProductSetConnector\Business;

use Codeception\TestCase\Test;
use Generated\Shared\Transfer\ProductSetTransfer;
use Spryker\Zed\CmsProductSetConnector\Business\CmsProductSetConnectorFacade;

/**
 * Auto-generated group annotations
 * @group SprykerTest
 * @group Zed
 * @group CmsProductSetConnector
 * @group Business
 * @group Facade
 * @group CmsProductSetConnectorFacadeTest
 * Add your own group annotations below this line
 */
class CmsProductSetConnectorFacadeTest extends Test
{

    /**
     * @return void
     */
    public function testMapProductKeyListShouldMapSetKeyToPrimaryKey()
    {
        $productAbstractTransfer1 = $this->tester->haveProductAbstract();
        $productAbstractTransfer2 = $this->tester->haveProductAbstract();

        $productSetTransfer = $this->tester->haveProductSet([
            ProductSetTransfer::ID_PRODUCT_ABSTRACTS => [
                $productAbstractTransfer1->getIdProductAbstract(),
                $productAbstractTransfer2->getIdProductAbstract(),
            ],
        ]);

        $cmsProductSetConnectorFacade = $this->createCmsProductSetConnectorFacade();
        $mappedProductSets = $cmsProductSetConnectorFacade->mapProductKeyList([$productSetTransfer->getProductSetKey()]);

        $this->assertCount(1, $mappedProductSets);
        $this->assertArrayHasKey($productSetTransfer->getProductSetKey(), $mappedProductSets);
        $this->assertEquals(
            $productSetTransfer->getIdProductSet(),
            $mappedProductSets[$productSetTransfer->getProductSetKey()]
        );
    }

    /**
     * @return \Spryker\Zed\CmsProductSetConnector\Business\CmsProductSetConnectorFacade
     */
    protected function createCmsProductSetConnectorFacade()
    {
        return new CmsProductSetConnectorFacade();
    }

}
