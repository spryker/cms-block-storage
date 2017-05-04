<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Cms\Business\Version\Handler;

use Orm\Zed\Cms\Persistence\Map\SpyCmsPageTableMap;
use Orm\Zed\Cms\Persistence\Map\SpyCmsTemplateTableMap;
use Spryker\Zed\Cms\Business\Template\TemplateManagerInterface;
use Spryker\Zed\Cms\Persistence\CmsQueryContainerInterface;

class CmsTemplateMigrationHandler implements MigrationHandlerInterface
{

    /**
     * @var \Spryker\Zed\Cms\Business\Template\TemplateManagerInterface
     */
    protected $templateManager;

    /**
     * @var \Spryker\Zed\Cms\Persistence\CmsQueryContainerInterface
     */
    protected $queryContainer;

    /**
     * @param \Spryker\Zed\Cms\Business\Template\TemplateManagerInterface $templateManager
     * @param \Spryker\Zed\Cms\Persistence\CmsQueryContainerInterface $queryContainer
     */
    public function __construct(TemplateManagerInterface $templateManager, CmsQueryContainerInterface $queryContainer)
    {
        $this->templateManager = $templateManager;
        $this->queryContainer = $queryContainer;
    }

    /**
     * @param array $originData
     * @param array $targetData
     *
     * @return void
     */
    public function handle(array $originData, array $targetData)
    {
        $templatePath = $targetData[SpyCmsTemplateTableMap::TABLE_NAME][SpyCmsTemplateTableMap::COL_TEMPLATE_PATH];
        $templateName = $targetData[SpyCmsTemplateTableMap::TABLE_NAME][SpyCmsTemplateTableMap::COL_TEMPLATE_NAME];
        $idCmsTemplate = $targetData[SpyCmsPageTableMap::COL_FK_TEMPLATE];

        if (!$this->templateManager->hasTemplatePath($templatePath)) {
            $cmsTemplateTransfer = $this->templateManager->createTemplate($templateName, $templatePath);
            $idCmsTemplate = $cmsTemplateTransfer->getIdCmsTemplate();
        }

        $cmsPageEntity = $this->queryContainer->queryPageById($targetData[SpyCmsPageTableMap::COL_ID_CMS_PAGE])->findOne();
        $cmsPageEntity->setFkTemplate($idCmsTemplate);

        $cmsPageEntity->save();
    }

}
