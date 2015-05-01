<?php

namespace SprykerFeature\Zed\UrlExporter\Business;

use Generated\Zed\Ide\FactoryAutoCompletion\UrlExporterBusiness;
use SprykerFeature\Shared\FrontendExporter\Code\KeyBuilder\KeyBuilderInterface;
use SprykerEngine\Zed\Kernel\Business\AbstractDependencyContainer;
use SprykerFeature\Zed\UrlExporter\Business\Builder\RedirectBuilderInterface;
use SprykerFeature\Zed\UrlExporter\Business\Builder\UrlBuilderInterface;

/**
 * @method UrlExporterBusiness getFactory()
 */
class UrlExporterDependencyContainer extends AbstractDependencyContainer
{
    /**
     * @return UrlBuilderInterface
     */
    public function getUrlMapBuilder()
    {
        return $this->getFactory()->createBuilderUrlBuilder(
            $this->getUrlKeyBuilder(),
            $this->getResourceKeyBuilder()
        );
    }

    /**
     * @return KeyBuilderInterface
     */
    protected function getUrlKeyBuilder()
    {
        return $this->getFactory()->createBuilderUrlKeyBuilder();
    }

    /**
     * @return KeyBuilderInterface
     */
    protected function getResourceKeyBuilder()
    {
        return $this->getFactory()->createBuilderResourceKeyBuilder();
    }

    /**
     * @return RedirectBuilderInterface
     */
    public function getRedirectBuilder()
    {
        return $this->getFactory()->createBuilderRedirectBuilder(
            $this->getResourceKeyBuilder()
        );
    }
}
