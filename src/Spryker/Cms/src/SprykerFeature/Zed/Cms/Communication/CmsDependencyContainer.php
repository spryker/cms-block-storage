<?php

namespace SprykerFeature\Zed\Cms\Communication;

use Generated\Zed\Ide\FactoryAutoCompletion\CmsCommunication;
use SprykerEngine\Zed\Kernel\Communication\AbstractDependencyContainer;
use Symfony\Component\HttpFoundation\Request;
use SprykerFeature\Zed\Cms\Persistence\CmsQueryContainer;

/**
 * @method CmsCommunication getFactory()
 */
class CmsDependencyContainer extends AbstractDependencyContainer
{

    /**
     * @param Request $request
     *
     * @return mixed
     */
    public function createCmsGrid(Request $request)
    {
        return $this->getFactory()->createGridCmsGrid(
            $this->getQueryContainer()->queryPagesWithTemplates(),
            $request
        );
    }

    /**
     * @return CmsQueryContainer
     */
    public function getQueryContainer()
    {
        return $this->getLocator()->cms()->queryContainer();
    }
}
