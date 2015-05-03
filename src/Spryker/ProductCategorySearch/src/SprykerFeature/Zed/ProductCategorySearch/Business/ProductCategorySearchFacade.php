<?php

namespace SprykerFeature\Zed\ProductCategorySearch\Business;

use SprykerEngine\Shared\Locale\Dto\LocaleDto;
use SprykerEngine\Zed\Kernel\Business\AbstractFacade;

/**
 * @method ProductCategorySearchDependencyContainer getDependencyContainer()
 */
class ProductCategorySearchFacade extends AbstractFacade
{

    /**
     * @param array $resultSet
     * @param array $processedResultSet
     * @param LocaleDto $locale
     * @return array
     */
    public function processProductCategorySearchData(array &$resultSet, array $processedResultSet, LocaleDto $locale)
    {
        return $this->getDependencyContainer()
            ->createProductCategorySearchProcessor()
            ->process($resultSet, $processedResultSet, $locale);
    }
}
