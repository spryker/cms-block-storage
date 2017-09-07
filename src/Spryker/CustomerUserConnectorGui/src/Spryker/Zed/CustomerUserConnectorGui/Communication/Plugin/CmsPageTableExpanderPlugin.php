<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\CustomerUserConnectorGui\Communication\Plugin;

use Orm\Zed\Cms\Persistence\Map\SpyCmsPageTableMap;
use Spryker\Zed\CmsGui\Dependency\Plugin\CmsPageTableExpanderPluginInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;

/**
 * @method \Spryker\Zed\CustomerUserConnectorGui\Business\CustomerUserConnectorGuiFacade getFacade()
 * @method \Spryker\Zed\CustomerUserConnectorGui\Communication\CustomerUserConnectorGuiCommunicationFactory getFactory()
 * @method \Spryker\Zed\CustomerUserConnectorGui\CustomerUserConnectorGuiConfig getConfig()
 */
class CmsPageTableExpanderPlugin extends AbstractPlugin implements CmsPageTableExpanderPluginInterface
{

    /**
     * {@inheritdoc}
     *
     * @api
     *
     * @param array $cmsPage
     *
     * @return array
     */
    public function getViewButtonGroupPermanentItems(array $cmsPage)
    {
        return [
            [
                'title' => 'Preview',
                'url' => $this->getConfig()->getPreviewPageUrl($cmsPage[SpyCmsPageTableMap::COL_ID_CMS_PAGE]),
                'separated' => false,
                'options' => ['target' => '_blank'],
            ],
        ];
    }

}
