<?php

/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace Spryker\Zed\Maintenance\Business\DependencyTree\DependencyFilter;

interface DependencyFilterCompositeInterface extends DependencyFilterInterface
{

    /**
     * @param \Spryker\Zed\Maintenance\Business\DependencyTree\DependencyFilter\DependencyFilterInterface $dependencyFilter
     *
     * @return $this
     */
    public function addFilter(DependencyFilterInterface $dependencyFilter);

}
