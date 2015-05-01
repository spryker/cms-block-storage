<?php

namespace SprykerFeature\Zed\ProductFrontendExporterPriceConnector\Communication\Plugin;

use SprykerEngine\Shared\Dto\LocaleDto;
use SprykerFeature\Zed\FrontendExporter\Dependency\Plugin\QueryExpanderPluginInterface;
use SprykerEngine\Zed\Kernel\Communication\AbstractPlugin;
use SprykerFeature\Zed\ProductFrontendExporterPriceConnector\Communication\ProductFrontendExporterPriceConnectorDependencyContainer;
use Propel\Runtime\ActiveQuery\ModelCriteria;

/**
 * @method ProductFrontendExporterPriceConnectorDependencyContainer getDependencyContainer()
 */
class ProductPriceQueryExpanderPlugin extends AbstractPlugin implements QueryExpanderPluginInterface
{
    /**
     * @return string
     */
    public function getProcessableType()
    {
        return 'product';
    }

    /**
     * @param ModelCriteria $expandableQuery
     * @param LocaleDto $locale
     *
     * @return ModelCriteria
     */
    public function expandQuery(ModelCriteria $expandableQuery, LocaleDto $locale)
    {
        $priceTypeEntity = $this->getDependencyContainer()->getQueryContainer()->getFkDefaultPriceType($this->getDefaultPriceType())->findOne();
        return $this->getDependencyContainer()->getQueryContainer()->expandQuery($expandableQuery, $priceTypeEntity->getIdPriceType());
    }

    /**
     * @return string
     */
    protected function getDefaultPriceType()
    {
        return $this->getDependencyContainer()->getPriceProcessor()->getDefaultPriceType();
    }
    /**
     * @return int
     */
    public function getChunkSize()
    {
        return 100;
    }

}
