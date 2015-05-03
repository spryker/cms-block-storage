<?php

namespace SprykerFeature\Zed\UrlExporter\Communication\Plugin;

use SprykerEngine\Shared\Locale\Dto\LocaleDto;
use SprykerEngine\Zed\Kernel\Communication\AbstractPlugin;
use SprykerFeature\Zed\FrontendExporter\Dependency\Plugin\DataProcessorPluginInterface;
use SprykerFeature\Zed\UrlExporter\Communication\UrlExporterDependencyContainer;

/**
 * @method UrlExporterDependencyContainer getDependencyContainer()
 */
class UrlProcessorPlugin extends AbstractPlugin implements DataProcessorPluginInterface
{
    /**
     * @return string
     */
    public function getProcessableType()
    {
        return 'url';
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
        $processedResultSet = $this->getDependencyContainer()->getUrlExporterFacade()->buildUrlMap($resultSet, $locale);

        return $processedResultSet;
    }
}
