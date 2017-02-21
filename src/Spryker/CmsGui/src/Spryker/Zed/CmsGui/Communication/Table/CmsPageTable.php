<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\CmsGui\Communication\Table;

use Generated\Shared\Transfer\CmsPageAttributesTransfer;
use Orm\Zed\Cms\Persistence\Map\SpyCmsPageTableMap;
use Spryker\Shared\Url\Url;
use Spryker\Zed\CmsGui\CmsGuiConfig;
use Spryker\Zed\CmsGui\Communication\Controller\CreateGlossaryController;
use Spryker\Zed\CmsGui\Communication\Controller\EditPageController;
use Spryker\Zed\CmsGui\Communication\Controller\ListPageController;
use Spryker\Zed\CmsGui\Dependency\Facade\CmsGuiToCmsInterface;
use Spryker\Zed\CmsGui\Dependency\Facade\CmsGuiToLocaleInterface;
use Spryker\Zed\CmsGui\Dependency\QueryContainer\CmsGuiToCmsQueryContainerInterface;
use Spryker\Zed\Gui\Communication\Table\AbstractTable;
use Spryker\Zed\Gui\Communication\Table\TableConfiguration;

class CmsPageTable extends AbstractTable
{

    const ACTIONS = 'Actions';
    const URL_CMS_PAGE_ACTIVATE = '/cms-gui/edit-page/activate';
    const URL_CMS_PAGE_DEACTIVATE = '/cms-gui/edit-page/deactivate';

    const COL_URL = 'Url';
    const COL_TEMPLATE = 'template_name';
    const COL_NAME = 'name';
    const COL_CMS_URLS = 'cmsUrls';

    /**
     * @var \Spryker\Zed\CmsGui\Dependency\QueryContainer\CmsGuiToCmsQueryContainerInterface
     */
    protected $cmsQueryContainer;

    /**
     * @var \Spryker\Zed\CmsGui\Dependency\Facade\CmsGuiToLocaleInterface
     */
    protected $localeFacade;

    /**
     * @var \Spryker\Zed\CmsGui\CmsGuiConfig
     */
    protected $cmsGuiConfig;

    /**
     * @var \Spryker\Zed\CmsGui\Dependency\Facade\CmsGuiToCmsInterface
     */
    protected $cmsFacade;

    /**
     * @param \Spryker\Zed\CmsGui\Dependency\QueryContainer\CmsGuiToCmsQueryContainerInterface $cmsQueryContainer
     * @param \Spryker\Zed\CmsGui\Dependency\Facade\CmsGuiToLocaleInterface $localeFacade
     * @param \Spryker\Zed\CmsGui\CmsGuiConfig $cmsGuiConfig
     * @param \Spryker\Zed\CmsGui\Dependency\Facade\CmsGuiToCmsInterface $cmsFacade
     */
    public function __construct(
        CmsGuiToCmsQueryContainerInterface $cmsQueryContainer,
        CmsGuiToLocaleInterface $localeFacade,
        CmsGuiConfig $cmsGuiConfig,
        CmsGuiToCmsInterface $cmsFacade
    ) {
        $this->cmsQueryContainer = $cmsQueryContainer;
        $this->localeFacade = $localeFacade;
        $this->cmsGuiConfig = $cmsGuiConfig;
        $this->cmsFacade = $cmsFacade;
    }

    /**
     * @param \Spryker\Zed\Gui\Communication\Table\TableConfiguration $config
     *
     * @return \Spryker\Zed\Gui\Communication\Table\TableConfiguration
     */
    protected function configure(TableConfiguration $config)
    {
        $this->setHeaders($config);
        $this->setRawColumns($config);
        $this->setSortableFields($config);
        $this->setSearchableFields($config);
        $this->setDefaultSortField($config);

        return $config;
    }

    /**
     * @param \Spryker\Zed\Gui\Communication\Table\TableConfiguration $config
     *
     * @return array
     */
    protected function prepareData(TableConfiguration $config)
    {
        $localeTransfer = $this->localeFacade->getCurrentLocale();

        $cmsPageAttributesTransfer = new CmsPageAttributesTransfer();
        $cmsPageAttributesTransfer->setLocaleName($localeTransfer->getLocaleName());

        $urlPrefix = $this->cmsFacade->getPageUrlPrefix($cmsPageAttributesTransfer);
        $query = $this->cmsQueryContainer->queryPagesWithTemplatesForSelectedLocale($localeTransfer->getIdLocale());

        $queryResults = $this->runQuery($query, $config);

        $results = [];
        foreach ($queryResults as $item) {
            $results[] = $this->mapResults($item, $urlPrefix);
        }

        return $results;
    }

    /**
     * @param array $item
     *
     * @return string
     */
    protected function buildUrlList(array $item)
    {
        $cmsUrls = $this->extractUrls($item);
        return implode('<br />', $cmsUrls);
    }

    /**
     * @param array $item
     * @param string $urlPrefix
     *
     * @return array
     */
    protected function buildLinks(array $item, $urlPrefix)
    {
        $buttons = [];

        $buttons[] = $this->createViewButton($item);
        $buttons[] = $this->createViewInShopButton($item, $urlPrefix);
        $buttons[] = $this->createEditGlossaryButton($item);
        $buttons[] = $this->createEditPageButton($item);
        $buttons[] = $this->createCmsStateChangeButton($item);

        return $buttons;
    }

    /**
     * @param array $item
     * @param string $urlPrefix
     *
     * @return string
     */
    protected function createViewInShopButton(array $item, $urlPrefix)
    {
        $yvesHost = $this->cmsGuiConfig->findYvesHost();
        if ($yvesHost === null) {
            return '';
        }

        $currentLocaleUrl = $this->findCurrentLocaleUrl($item, $urlPrefix);
        if ($currentLocaleUrl === null) {
            return '';
        }

        $cmsPageUrlInYves = $yvesHost . $currentLocaleUrl;

        return $this->generateViewButton(
            $cmsPageUrlInYves,
            'View in Shop',
            ['target' => '_blank']
        );
    }

    /**
     * @param array $item
     * @param string $urlPrefix
     *
     * @return string|null
     */
    protected function findCurrentLocaleUrl(array $item, $urlPrefix)
    {
        $cmsUrls = $this->extractUrls($item);
        foreach ($cmsUrls as $url) {
            if (preg_match('#^' . $urlPrefix . '#i', $url) > 0) {
                return $url;
            }
        }

        if (count($cmsUrls) > 0) {
            return $cmsUrls[0];
        }

        return null;
    }

    /**
     * @param array $item
     *
     * @return string
     */
    protected function createViewButton(array $item)
    {
        return $this->generateViewButton(
            Url::generate('/cms-gui/view-page/index', [
                ListPageController::URL_PARAM_ID_CMS_PAGE => $item[SpyCmsPageTableMap::COL_ID_CMS_PAGE],
            ]),
            'View'
        );
    }

    /**
     * @param array $item
     *
     * @return string
     */
    protected function createEditGlossaryButton(array $item)
    {
        return $this->generateEditButton(
            Url::generate('/cms-gui/create-glossary/index', [
                CreateGlossaryController::URL_PARAM_ID_CMS_PAGE => $item[SpyCmsPageTableMap::COL_ID_CMS_PAGE],
            ]),
            'Edit Placeholders'
        );
    }

    /**
     * @param array $item
     *
     * @return string
     */
    protected function createEditPageButton(array $item)
    {
        return $this->generateEditButton(
            Url::generate('/cms-gui/edit-page/index', [
                EditPageController::URL_PARAM_ID_CMS_PAGE => $item[SpyCmsPageTableMap::COL_ID_CMS_PAGE],
            ]),
            'Edit Page'
        );
    }

    /**
     * @param array $item
     *
     * @return string
     */
    protected function createCmsStateChangeButton(array $item)
    {
        if ($item[SpyCmsPageTableMap::COL_IS_ACTIVE]) {
            return $this->generateRemoveButton(
                Url::generate(static::URL_CMS_PAGE_DEACTIVATE, [
                    EditPageController::URL_PARAM_ID_CMS_PAGE => $item[SpyCmsPageTableMap::COL_ID_CMS_PAGE],
                    EditPageController::URL_PARAM_REDIRECT_URL => '/cms-gui/list-page/index',
                ]),
                'Deactivate'
            );
        }

        return $this->generateViewButton(
            Url::generate(static::URL_CMS_PAGE_ACTIVATE, [
                EditPageController::URL_PARAM_ID_CMS_PAGE => $item[SpyCmsPageTableMap::COL_ID_CMS_PAGE],
                EditPageController::URL_PARAM_REDIRECT_URL => '/cms-gui/list-page/index',
            ]),
            'Activate'
        );
    }

    /**
     * @param array $item
     *
     * @return string
     */
    protected function getStatusLabel($item)
    {
        if (!$item[SpyCmsPageTableMap::COL_IS_ACTIVE]) {
            return '<span class="label label-danger">Inactive</span>';
        }

        return '<span class="label label-info">Active</span>';
    }

    /**
     * @param array $item
     *
     * @return array
     */
    protected function extractUrls(array $item)
    {
        return explode(',', $item[static::COL_CMS_URLS]);
    }

    /**
     * @param \Spryker\Zed\Gui\Communication\Table\TableConfiguration $config
     *
     * @return void
     */
    protected function setHeaders(TableConfiguration $config)
    {
        $config->setHeader([
            SpyCmsPageTableMap::COL_ID_CMS_PAGE => '#',
            static::COL_NAME => 'Name',
            static::COL_URL => 'Url',
            static::COL_TEMPLATE => 'Template',
            SpyCmsPageTableMap::COL_IS_ACTIVE => 'Active',
            static::ACTIONS => static::ACTIONS,
        ]);
    }

    /**
     * @param \Spryker\Zed\Gui\Communication\Table\TableConfiguration $config
     *
     * @return void
     */
    protected function setRawColumns(TableConfiguration $config)
    {
        $config->setRawColumns([
            static::ACTIONS,
            SpyCmsPageTableMap::COL_IS_ACTIVE,
            static::COL_URL,
        ]);
    }

    /**
     * @param \Spryker\Zed\Gui\Communication\Table\TableConfiguration $config
     *
     * @return void
     */
    protected function setSortableFields(TableConfiguration $config)
    {
        $config->setSortable([
            SpyCmsPageTableMap::COL_ID_CMS_PAGE,
            SpyCmsPageTableMap::COL_IS_ACTIVE,
            static::COL_TEMPLATE,
            static::COL_NAME,
        ]);
    }

    /**
     * @param \Spryker\Zed\Gui\Communication\Table\TableConfiguration $config
     *
     * @return void
     */
    protected function setSearchableFields(TableConfiguration $config)
    {
        $config->setSearchable([
            SpyCmsPageTableMap::COL_ID_CMS_PAGE,
            static::COL_NAME,
            static::COL_TEMPLATE,
        ]);
    }

    /**
     * @param \Spryker\Zed\Gui\Communication\Table\TableConfiguration $config
     *
     * @return void
     */
    protected function setDefaultSortField(TableConfiguration $config)
    {
        $config->setDefaultSortField(SpyCmsPageTableMap::COL_ID_CMS_PAGE, TableConfiguration::SORT_DESC);
    }

    /**
     * @param array $item
     * @param string $urlPrefix
     *
     * @return array
     */
    protected function mapResults(array $item, $urlPrefix)
    {
        $actions = implode(' ', $this->buildLinks($item, $urlPrefix));
        return [
            SpyCmsPageTableMap::COL_ID_CMS_PAGE => $item[SpyCmsPageTableMap::COL_ID_CMS_PAGE],
            static::COL_NAME => $item[static::COL_NAME],
            static::COL_URL => $this->buildUrlList($item),
            static::COL_TEMPLATE => $item[static::COL_TEMPLATE],
            SpyCmsPageTableMap::COL_IS_ACTIVE => $this->getStatusLabel($item),
            static::ACTIONS => $actions,
        ];
    }

}
