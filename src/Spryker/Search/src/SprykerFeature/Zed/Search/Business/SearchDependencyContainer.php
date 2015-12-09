<?php

/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace SprykerFeature\Zed\Search\Business;

use Generated\Zed\Ide\FactoryAutoCompletion\SearchBusiness;
use SprykerEngine\Shared\Kernel\Messenger\MessengerInterface;
use SprykerEngine\Zed\Kernel\Business\AbstractBusinessDependencyContainer;
use SprykerFeature\Zed\Search\Business\Model\Search;
use SprykerFeature\Zed\Search\Business\Model\SearchInstaller;
use SprykerFeature\Zed\Search\SearchConfig;
use SprykerFeature\Zed\Search\SearchDependencyProvider;

/**
 * @method SearchConfig getConfig()
 */
class SearchDependencyContainer extends AbstractBusinessDependencyContainer
{

    /**
     * @param MessengerInterface $messenger
     *
     * @return SearchInstaller
     */
    public function createSearchInstaller(MessengerInterface $messenger)
    {
        return new SearchInstaller(
            $this->getConfig()->getInstaller(),
            $messenger
        );
    }

    /**
     * @return Search
     */
    public function createSearch()
    {
        return new Search(
            $this->getProvidedDependency(SearchDependencyProvider::CLIENT_SEARCH)
        );
    }

}
