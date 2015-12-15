<?php

/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace Spryker\Zed\Tax\Dependency\Plugin;

interface TaxChangePluginInterface
{

    /**
     * @param int $idTaxRate
     */
    public function handleTaxRateChange($idTaxRate);

    /**
     * @param int $idTaxSet
     */
    public function handleTaxSetChange($idTaxSet);

}
