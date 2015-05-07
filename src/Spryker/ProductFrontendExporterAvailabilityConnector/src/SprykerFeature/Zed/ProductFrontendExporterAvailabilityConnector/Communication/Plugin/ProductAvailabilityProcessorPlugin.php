<?php

namespace SprykerFeature\Zed\ProductFrontendExporterAvailabilityConnector\Communication\Plugin;

use SprykerEngine\Shared\Locale\Dto\LocaleDto;
use SprykerEngine\Zed\Kernel\Communication\AbstractPlugin;
use SprykerFeature\Zed\FrontendExporter\Dependency\Plugin\DataProcessorPluginInterface;

class ProductAvailabilityProcessorPlugin extends AbstractPlugin implements DataProcessorPluginInterface
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
        foreach ($resultSet as $index => $productRawData) {
            if (isset($processedResultSet[$index])) {
                $processedResultSet[$index]['available'] = ($productRawData['quantity'] > 0);
            }
        }

        return $processedResultSet;
    }
}
