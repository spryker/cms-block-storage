<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductOptionCartConnector\Business;

use Spryker\Zed\Kernel\Business\AbstractBusinessFactory;
use Spryker\Zed\ProductOptionCartConnector\Business\Manager\ProductOptionManager;
use Spryker\Zed\ProductOptionCartConnector\Business\Model\GroupKeyExpander;
use Spryker\Zed\ProductOptionCartConnector\ProductOptionCartConnectorDependencyProvider;

/**
 * @method \Spryker\Zed\ProductOptionCartConnector\Business\ProductOptionCartConnectorBusinessFactory getFactory()
 * @method \Spryker\Zed\ProductOptionCartConnector\ProductOptionCartConnectorConfig getConfig()
 */
class ProductOptionCartConnectorBusinessFactory extends AbstractBusinessFactory
{

    /**
     * @return \Spryker\Zed\ProductOptionCartConnector\Business\Manager\ProductOptionManagerInterface
     */
    public function createProductOptionManager()
    {
        return new ProductOptionManager(
            $this->getProvidedDependency(ProductOptionCartConnectorDependencyProvider::FACADE_PRODUCT_OPTION)
        );
    }

    /**
     * @return \Spryker\Zed\ProductOptionCartConnector\Business\Model\GroupKeyExpander
     */
    public function createGroupKeyExpander()
    {
        return new GroupKeyExpander();
    }

}
