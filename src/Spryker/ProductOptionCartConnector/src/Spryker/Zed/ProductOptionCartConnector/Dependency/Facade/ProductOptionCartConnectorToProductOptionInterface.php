<?php
/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace Spryker\Zed\ProductOptionCartConnector\Dependency\Facade;

interface ProductOptionCartConnectorToProductOptionInterface
{

    /**
     * @param int $idProductOptionValueUsage
     * @param int $idLocale
     *
     * @return \Generated\Shared\Transfer\ProductOptionTransfer
     */
    public function getProductOption($idProductOptionValueUsage, $idLocale);

}
