<?php
/**
 * Copyright © 2017-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Cms\Business\Version;

use Generated\Shared\Transfer\CmsVersionTransfer;
use Orm\Zed\Cms\Persistence\Map\SpyCmsGlossaryKeyMappingTableMap;
use Orm\Zed\Cms\Persistence\Map\SpyCmsPageLocalizedAttributesTableMap;
use Orm\Zed\Cms\Persistence\Map\SpyCmsTemplateTableMap;
use Orm\Zed\Cms\Persistence\SpyCmsVersion;
use Orm\Zed\Glossary\Persistence\Map\SpyGlossaryKeyTableMap;
use Orm\Zed\Glossary\Persistence\Map\SpyGlossaryTranslationTableMap;
use Orm\Zed\Locale\Persistence\Map\SpyLocaleTableMap;
use Propel\Runtime\Map\TableMap;
use Spryker\Shared\Cms\CmsConstants;
use Spryker\Zed\Cms\Business\Exception\MissingPageException;
use Spryker\Zed\Cms\Dependency\CmsVersionPostSavePluginInterface;
use Spryker\Zed\Cms\Dependency\Facade\CmsToTouchInterface;
use Spryker\Zed\Cms\Persistence\CmsQueryContainerInterface;

class VersionPublisher implements VersionPublisherInterface
{

    const CMS_TEMPLATE = 'CmsTemplate';
    const LOCALE = 'Locale';
    const CMS_PAGE_LOCALIZED_ATTRIBUTES = 'SpyCmsPageLocalizedAttributess';
    const CMS_GLOSSARY_KEY_MAPPINGS = 'SpyCmsGlossaryKeyMappings';
    const GLOSSARY_KEY = 'GlossaryKey';

    /**
     * @var VersionGeneratorInterface
     */
    protected $versionGenerator;

    /**
     * @var \Spryker\Zed\Cms\Dependency\Facade\CmsToTouchInterface
     */
    protected $touchFacade;

    /**
     * @var CmsQueryContainerInterface
     */
    protected $queryContainer;

    /**
     * @var CmsVersionPostSavePluginInterface[]
     */
    protected $postSavePlugins = [];

    /**
     * @param VersionGeneratorInterface $versionGenerator
     * @param \Spryker\Zed\Cms\Dependency\Facade\CmsToTouchInterface $touchFacade
     * @param CmsQueryContainerInterface $queryContainer
     * @param CmsVersionPostSavePluginInterface[] $postSavePlugins
     */
    public function __construct(VersionGeneratorInterface $versionGenerator, CmsToTouchInterface $touchFacade, CmsQueryContainerInterface $queryContainer, array $postSavePlugins)
    {
        $this->versionGenerator = $versionGenerator;
        $this->touchFacade = $touchFacade;
        $this->queryContainer = $queryContainer;
        $this->postSavePlugins = $postSavePlugins;
    }

    /**
     * @param int $idCmsPage
     * @param string|null $versionName
     *
     * @throws MissingPageException
     *
     * @return CmsVersionTransfer
     */
    public function publishAndVersion($idCmsPage, $versionName = null)
    {
        $cmsPageArray = $this->queryContainer
            ->queryCmsPageWithAllRelationsEntitiesByIdPage($idCmsPage)
            ->find()
            ->toArray(null, false, TableMap::TYPE_COLNAME);

        if (empty($cmsPageArray)) {
            throw new MissingPageException(
                sprintf(
                    'There is no valid Cms page with this id: %d . If the page exists. please check the placeholders',
                    $idCmsPage
                )
            );
        }

        return $this->createCmsVersion($this->encodeCmsData($cmsPageArray), $idCmsPage, $versionName);
    }

    /**
     * @param array $data
     *
     * @return string
     */
    protected function encodeCmsData(array $data)
    {
        $cmsVersionArray = current($data);

        $cmsVersionArray[SpyCmsTemplateTableMap::TABLE_NAME] = $this->formatCmsTemplateData($cmsVersionArray);
        $cmsVersionArray[SpyCmsPageLocalizedAttributesTableMap::TABLE_NAME] = $this->formatCmsPageLocalizedAttributesData($cmsVersionArray);
        $cmsVersionArray[SpyCmsGlossaryKeyMappingTableMap::TABLE_NAME] = $this->formatCmsGlossaryKeyMappingsData($cmsVersionArray);

        $cmsVersionArray = $this->unsetBadFormattedData($cmsVersionArray);

        return json_encode($cmsVersionArray);
    }

    /**
     * @param array $cmsVersionArray
     *
     * @return array
     */
    protected function unsetBadFormattedData(array $cmsVersionArray)
    {
        unset($cmsVersionArray[static::CMS_TEMPLATE]);
        unset($cmsVersionArray[static::CMS_PAGE_LOCALIZED_ATTRIBUTES]);
        unset($cmsVersionArray[static::CMS_GLOSSARY_KEY_MAPPINGS]);

        return $cmsVersionArray;
    }

    /**
     * @param array $cmsVersionArray
     *
     * @return array
     */
    protected function formatCmsTemplateData(array $cmsVersionArray)
    {
        $templateData = [];
        $templateData[SpyCmsTemplateTableMap::COL_TEMPLATE_PATH] = $cmsVersionArray[static::CMS_TEMPLATE][SpyCmsTemplateTableMap::COL_TEMPLATE_PATH];
        $templateData[SpyCmsTemplateTableMap::COL_TEMPLATE_NAME] = $cmsVersionArray[static::CMS_TEMPLATE][SpyCmsTemplateTableMap::COL_TEMPLATE_NAME];

        return $templateData;
    }

    /**
     * @param array $cmsVersionArray
     *
     * @return array
     */
    protected function formatCmsPageLocalizedAttributesData(array $cmsVersionArray)
    {
        $mappedLocalizedAttributesByLocaleName = [];
        $localizedAttributesItems = $cmsVersionArray[static::CMS_PAGE_LOCALIZED_ATTRIBUTES];
        foreach ($localizedAttributesItems as $localizedAttributesItem) {
            $formattedItem = [];
            $formattedItem[SpyCmsPageLocalizedAttributesTableMap::COL_NAME] = $localizedAttributesItem[SpyCmsPageLocalizedAttributesTableMap::COL_NAME];
            $formattedItem[SpyCmsPageLocalizedAttributesTableMap::COL_META_TITLE] = $localizedAttributesItem[SpyCmsPageLocalizedAttributesTableMap::COL_META_TITLE];
            $formattedItem[SpyCmsPageLocalizedAttributesTableMap::COL_META_KEYWORDS] = $localizedAttributesItem[SpyCmsPageLocalizedAttributesTableMap::COL_META_KEYWORDS];
            $formattedItem[SpyCmsPageLocalizedAttributesTableMap::COL_META_DESCRIPTION] = $localizedAttributesItem[SpyCmsPageLocalizedAttributesTableMap::COL_META_DESCRIPTION];

            $localeName = $localizedAttributesItem[static::LOCALE][SpyLocaleTableMap::COL_LOCALE_NAME];
            $mappedLocalizedAttributesByLocaleName[$localeName] = $formattedItem;
        }

        return $mappedLocalizedAttributesByLocaleName;
    }

    /**
     * @param array $cmsVersionArray
     *
     * @return array
     */
    protected function formatCmsGlossaryKeyMappingsData(array $cmsVersionArray)
    {
        $filteredGlossaryKeyMappings = [];
        $glossaryKeyMappingItems = $cmsVersionArray[static::CMS_GLOSSARY_KEY_MAPPINGS];
        foreach ($glossaryKeyMappingItems as $glossaryKeyMappingItem) {
            $formattedSpyCmsGlossaryKeyMappingItem = [];
            $formattedSpyCmsGlossaryKeyMappingItem[SpyCmsGlossaryKeyMappingTableMap::COL_PLACEHOLDER] = $glossaryKeyMappingItem[SpyCmsGlossaryKeyMappingTableMap::COL_PLACEHOLDER];
            $formattedSpyCmsGlossaryKeyMappingItem = $this->extractGlossaryKey($glossaryKeyMappingItem, $formattedSpyCmsGlossaryKeyMappingItem);
            $formattedSpyCmsGlossaryKeyMappingItem = $this->extractGlossaryTranslations($glossaryKeyMappingItem, $formattedSpyCmsGlossaryKeyMappingItem);
            $filteredGlossaryKeyMappings[] = $formattedSpyCmsGlossaryKeyMappingItem;
        }

        return $filteredGlossaryKeyMappings;
    }

    /**
     * @param array $glossaryKeyMappingItem
     * @param array $formattedSpyCmsGlossaryKeyMappingItem
     *
     * @return array
     */
    protected function extractGlossaryKey(array $glossaryKeyMappingItem, array $formattedSpyCmsGlossaryKeyMappingItem)
    {
        $glossaryKey = $glossaryKeyMappingItem[static::GLOSSARY_KEY][SpyGlossaryKeyTableMap::COL_KEY];
        $formattedSpyCmsGlossaryKeyMappingItem[SpyGlossaryKeyTableMap::TABLE_NAME][SpyGlossaryKeyTableMap::COL_KEY] = $glossaryKey;

        return $formattedSpyCmsGlossaryKeyMappingItem;
    }

    /**
     * @param array $glossaryKeyMappingItem
     * @param array $formattedSpyCmsGlossaryKeyMappingItem
     *
     * @return array
     */
    protected function extractGlossaryTranslations(array $glossaryKeyMappingItem, array $formattedSpyCmsGlossaryKeyMappingItem)
    {
        $glossaryTranslations = $this->mapGlossaryTranslationsToLocale($glossaryKeyMappingItem[static::GLOSSARY_KEY]['SpyGlossaryTranslations']);
        $formattedSpyCmsGlossaryKeyMappingItem[SpyGlossaryKeyTableMap::TABLE_NAME][SpyGlossaryTranslationTableMap::TABLE_NAME] = $glossaryTranslations;

        return $formattedSpyCmsGlossaryKeyMappingItem;
    }

    /**
     * @param array $glossaryTranslationItems
     *
     * @return array
     */
    protected function mapGlossaryTranslationsToLocale(array $glossaryTranslationItems)
    {
        $mappedGlossaryTranslationsByLocaleName = [];
        foreach ($glossaryTranslationItems as $glossaryTranslationItem) {
            $formattedItem = [];
            $formattedItem[SpyGlossaryTranslationTableMap::COL_VALUE] = $glossaryTranslationItem[SpyGlossaryTranslationTableMap::COL_VALUE];
            $localeName = $glossaryTranslationItem[static::LOCALE][SpyLocaleTableMap::COL_LOCALE_NAME];
            $mappedGlossaryTranslationsByLocaleName[$localeName] = $formattedItem;
        }

        return $mappedGlossaryTranslationsByLocaleName;
    }

    /**
     * @param string $data
     * @param int $idCmsPage
     * @param string|null $versionName
     *
     * @return CmsVersionTransfer
     */
    protected function createCmsVersion($data, $idCmsPage, $versionName = null)
    {
        $versionNumber = $this->versionGenerator->generateNewCmsVersion($idCmsPage);

        if ($versionName === null) {
            $versionName = $this->versionGenerator->generateNewCmsVersionName($versionNumber);
        }

        $cmsVersionTransfer = $this->saveCmsVersion(
            $idCmsPage,
            $data,
            $versionNumber,
            $versionName
        );

        foreach ($this->postSavePlugins as $userPlugin) {
            $cmsVersionTransfer = $userPlugin->postSave($cmsVersionTransfer);
        }

        $this->touchFacade->touchActive(CmsConstants::RESOURCE_TYPE_PAGE, $idCmsPage);

        return $cmsVersionTransfer;
    }

    /**
     * @param int $idCmsPage
     * @param string $data
     * @param int $versionNumber
     * @param string $versionName
     *
     * @return CmsVersionTransfer
     */
    protected function saveCmsVersion($idCmsPage, $data, $versionNumber, $versionName)
    {
        $cmsVersionEntity = new SpyCmsVersion();
        $cmsVersionEntity->setFkCmsPage($idCmsPage);
        $cmsVersionEntity->setData($data);
        $cmsVersionEntity->setVersion($versionNumber);
        $cmsVersionEntity->setVersionName($versionName);
        $cmsVersionEntity->save();

        return $this->convertToCmsVersionTransfer($cmsVersionEntity);
    }

    /**
     * @param SpyCmsVersion $cmsVersionEntity
     *
     * @return CmsVersionTransfer
     */
    protected function convertToCmsVersionTransfer(SpyCmsVersion $cmsVersionEntity)
    {
        $cmsVersionTransfer = new CmsVersionTransfer();
        $cmsVersionTransfer->fromArray($cmsVersionEntity->toArray(), true);

        return $cmsVersionTransfer;
    }
}
