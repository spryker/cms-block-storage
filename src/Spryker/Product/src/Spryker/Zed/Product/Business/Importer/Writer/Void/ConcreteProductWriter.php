<?php

/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace Spryker\Zed\Product\Business\Importer\Writer\Void;

use Generated\Shared\Transfer\ConcreteProductTransfer;
use Spryker\Zed\Product\Business\Importer\Writer\ConcreteProductWriterInterface;

/**
 * Class ConcreteProductWriter
 */
class ConcreteProductWriter implements ConcreteProductWriterInterface
{

    /**
     * @param ConcreteProductTransfer $product
     *
     * @return bool success
     */
    public function writeProduct(ConcreteProductTransfer $product)
    {
        return is_object($product);
    }

}
