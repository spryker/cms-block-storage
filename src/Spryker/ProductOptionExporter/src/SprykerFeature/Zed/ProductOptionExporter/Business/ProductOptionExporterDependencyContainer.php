<?php
/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace SprykerFeature\Zed\ProductOptionExporter\Business;

use SprykerEngine\Zed\Kernel\Business\AbstractDependencyContainer;
use SprykerFeature\Zed\ProductOptionExporter\Business\Model\ExportProcessorInterface;
use SprykerFeature\Zed\ProductOptionExporter\ProductOptionExporterDependencyProvider;
use Generated\Zed\Ide\FactoryAutoCompletion\ProductOptionExporterBusiness;


/**
 * @method ProductOptionExporterBusiness getFactory()
 */
class ProductOptionExporterDependencyContainer extends AbstractDependencyContainer
{

    /**
     * @return ExportProcessorInterface
     */
    public function getProcessorModel()
    {
        return $this->getFactory()->createModelExportProcessor(
            $this->getProvidedDependency(ProductOptionExporterDependencyProvider::FACADE_PRODUCT_OPTION),
            $this->getProvidedDependency(ProductOptionExporterDependencyProvider::FACADE_PRODUCT),
            $this->getProvidedDependency(ProductOptionExporterDependencyProvider::FACADE_LOCALE)
        );
    }
}
