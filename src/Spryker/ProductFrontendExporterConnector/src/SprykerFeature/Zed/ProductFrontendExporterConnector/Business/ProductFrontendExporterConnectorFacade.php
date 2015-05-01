<?php


namespace SprykerFeature\Zed\ProductFrontendExporterConnector\Business;

use SprykerEngine\Shared\Dto\LocaleDto;
use SprykerFeature\Zed\FrontendExporter\Business\Model\BatchResultInterface;
use SprykerEngine\Zed\Kernel\Business\AbstractFacade;

/**
 * @method ProductFrontendExporterConnectorDependencyContainer getDependencyContainer()
 */
class ProductFrontendExporterConnectorFacade extends AbstractFacade
{
    /**
     * @param array $products
     * @param LocaleDto $locale
     *
     * @return array
     */
    public function buildProducts(array $products, LocaleDto $locale)
    {
        return $this->getDependencyContainer()
            ->getProductProcessor()
            ->buildProducts($products, $locale);
    }

    /**
     * @param BatchResultInterface $result
     *
     * @return bool
     */
    public function isExportFailed(BatchResultInterface $result)
    {
        return $this->getDependencyContainer()->getProductExportDecider()->isFailed($result);
    }
}
