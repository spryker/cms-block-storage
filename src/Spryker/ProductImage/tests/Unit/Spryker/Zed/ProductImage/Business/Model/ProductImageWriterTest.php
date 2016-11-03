<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Unit\Spryker\Zed\ProductImage\Business\Model;

use Codeception\TestCase\Test;
use Generated\Shared\Transfer\ProductImageSetTransfer;
use Generated\Shared\Transfer\ProductImageTransfer;
use Spryker\Zed\Locale\Business\LocaleFacade;
use Spryker\Zed\ProductImage\Business\Model\Writer;
use Spryker\Zed\ProductImage\Persistence\ProductImageQueryContainer;

/**
 * @group Unit
 * @group Spryker
 * @group Zed
 * @group ProductImage
 * @group Business
 * @group Model
 * @group ProductImageWriterTest
 */
class ProductImageWriterTest extends Test
{

    /**
     * @var \Spryker\Zed\ProductImage\Persistence\ProductImageQueryContainerInterface
     */
    protected $queryContainer;

    /**
     * @var \Spryker\Zed\ProductImage\Business\Model\WriterInterface
     */
    protected $writer;

    /**
     * @var \Spryker\Zed\Locale\Business\LocaleFacadeInterface
     */
    protected $localeFacade;

    /**
     * @var \Spryker\Zed\ProductImage\Business\Transfer\ProductImageTransferMapperInterface
     */
    protected $transferGenerator;

    protected function setUp()
    {
        $this->writer = new Writer(
            new ProductImageQueryContainer()
        );

        $this->localeFacade = new LocaleFacade();
    }

    public function testPersistProductImage()
    {
        $imageTransfer = new ProductImageTransfer();
        $imageTransfer
            ->setSortOrder(11)
            ->setExternalUrlLarge('large')
            ->setExternalUrlSmall('small');

        $imageTransfer = $this->writer
            ->saveProductImage($imageTransfer);

        $this->assertInstanceOf(ProductImageTransfer::class, $imageTransfer);

        //$this->assertNotNull($imageTransfer->getIdProductImage());
    }

    public function testPersistProductImageSet()
    {
        $imageTransfer = new ProductImageTransfer();
        $imageTransfer
            ->setSortOrder(7)
            ->setExternalUrlLarge('large')
            ->setExternalUrlSmall('small');

        $imageCollection = new \ArrayObject([$imageTransfer]);

        $imageSetTransfer = new ProductImageSetTransfer();
        $imageSetTransfer
            ->setIdProductAbstract(1)
            ->setName('Foo')
            ->setProductImages($imageCollection);

        $imageSetTransfer = $this->writer
            ->saveProductImageSet($imageSetTransfer);

        $this->assertInstanceOf(ProductImageSetTransfer::class, $imageSetTransfer);
        $this->assertInstanceOf(ProductImageTransfer::class, $imageSetTransfer->getProductImages()[0]);
    }

}
