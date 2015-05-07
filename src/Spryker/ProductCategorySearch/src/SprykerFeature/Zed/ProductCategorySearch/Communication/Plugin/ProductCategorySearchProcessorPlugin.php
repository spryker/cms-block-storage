<?php

namespace SprykerFeature\Zed\ProductCategorySearch\Communication\Plugin;

use SprykerEngine\Shared\Locale\Dto\LocaleDto;
use SprykerEngine\Zed\Kernel\Communication\AbstractPlugin;
use SprykerFeature\Zed\FrontendExporter\Dependency\Plugin\DataProcessorPluginInterface;
use SprykerFeature\Zed\ProductCategorySearch\Communication\ProductCategorySearchDependencyContainer;

/**
 * Class ProductCategoryProcessorPlugin
 * @package SprykerFeature\Zed\ProductCategory\Communication\Plugin
 */
/**
 * @method ProductCategorySearchDependencyContainer getDependencyContainer()
 */
class ProductCategorySearchProcessorPlugin extends AbstractPlugin implements DataProcessorPluginInterface
{
    /**
     * @return string
     */
    public function getProcessableType()
    {
        return 'abstract_product';
    }

    /**
     * @param array $resultSet
     * @param array $processedResultSet
     * @param LocaleDto $locale
     *
     * @return array
     */
    public function processData(array &$resultSet, array $processedResultSet, LocaleDto $locale)
    {
        $facade = $this->getDependencyContainer()->getProductCategorySearchFacade();

        return $facade->processProductCategorySearchData($resultSet, $processedResultSet, $locale);
    }
}
