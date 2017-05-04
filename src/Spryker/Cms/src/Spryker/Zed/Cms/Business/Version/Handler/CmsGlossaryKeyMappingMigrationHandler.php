<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Cms\Business\Version\Handler;

use ArrayObject;
use Generated\Shared\Transfer\CmsGlossaryAttributesTransfer;
use Generated\Shared\Transfer\CmsGlossaryTransfer;
use Generated\Shared\Transfer\CmsPlaceholderTranslationTransfer;
use Generated\Shared\Transfer\LocaleTransfer;
use Orm\Zed\Cms\Persistence\Map\SpyCmsGlossaryKeyMappingTableMap;
use Orm\Zed\Cms\Persistence\Map\SpyCmsPageTableMap;
use Orm\Zed\Cms\Persistence\Map\SpyCmsTemplateTableMap;
use Orm\Zed\Glossary\Persistence\Map\SpyGlossaryKeyTableMap;
use Orm\Zed\Glossary\Persistence\Map\SpyGlossaryTranslationTableMap;
use Spryker\Zed\Cms\Business\Mapping\CmsGlossarySaverInterface;
use Spryker\Zed\Cms\Dependency\Facade\CmsToLocaleInterface;
use Spryker\Zed\Cms\Persistence\CmsQueryContainerInterface;

class CmsGlossaryKeyMappingMigrationHandler implements MigrationHandlerInterface
{

    /**
     * @var \Spryker\Zed\Cms\Business\Mapping\CmsGlossarySaverInterface
     */
    protected $cmsGlossarySaver;

    /**
     * @var \Spryker\Zed\Cms\Dependency\Facade\CmsToLocaleInterface
     */
    protected $localeFacade;

    /**
     * @var \Spryker\Zed\Cms\Persistence\CmsQueryContainerInterface
     */
    protected $queryContainer;

    /**
     * @param \Spryker\Zed\Cms\Business\Mapping\CmsGlossarySaverInterface $cmsGlossarySaver
     * @param \Spryker\Zed\Cms\Dependency\Facade\CmsToLocaleInterface $localeFacade
     * @param \Spryker\Zed\Cms\Persistence\CmsQueryContainerInterface $queryContainer
     */
    public function __construct(CmsGlossarySaverInterface $cmsGlossarySaver, CmsToLocaleInterface $localeFacade, CmsQueryContainerInterface $queryContainer)
    {
        $this->cmsGlossarySaver = $cmsGlossarySaver;
        $this->localeFacade = $localeFacade;
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
        $this->cmsGlossarySaver->deleteCmsGlossary($originData[SpyCmsPageTableMap::COL_ID_CMS_PAGE]);

        $glossaryAttributeTransfers = $this->createCmsGlossaryAttributeTransfers(
            $targetData[SpyCmsGlossaryKeyMappingTableMap::TABLE_NAME],
            $originData[SpyCmsPageTableMap::COL_ID_CMS_PAGE],
            $targetData[SpyCmsTemplateTableMap::TABLE_NAME][SpyCmsTemplateTableMap::COL_TEMPLATE_NAME]
        );

        $this->cmsGlossarySaver->saveCmsGlossary((new CmsGlossaryTransfer())->setGlossaryAttributes($glossaryAttributeTransfers));
    }

    /**
     * @param array $glossaryKeyMappings
     * @param int $idCmsPage
     * @param string $templateName
     *
     * @return \ArrayObject
     */
    protected function createCmsGlossaryAttributeTransfers(array $glossaryKeyMappings, $idCmsPage, $templateName)
    {
        $glossaryAttributeTransfers = new ArrayObject();
        foreach ($glossaryKeyMappings as $glossaryKeyMapping) {
            $translations = $this->createCmsPlaceholderTranslationTransfers(
                $glossaryKeyMapping[SpyGlossaryKeyTableMap::TABLE_NAME][SpyGlossaryTranslationTableMap::TABLE_NAME]
            );

            $glossaryAttributeTransfers[] = $this->createGlossaryAttributeTransfer(
                $translations,
                $idCmsPage,
                $glossaryKeyMapping[SpyCmsGlossaryKeyMappingTableMap::COL_PLACEHOLDER],
                $templateName
            );
        }

        return $glossaryAttributeTransfers;
    }

    /**
     * @param \ArrayObject $translations
     * @param int $idCmsPage
     * @param string $placeholder
     * @param string $templateName
     *
     * @return \Generated\Shared\Transfer\CmsGlossaryAttributesTransfer
     */
    protected function createGlossaryAttributeTransfer(ArrayObject $translations, $idCmsPage, $placeholder, $templateName)
    {
        $glossaryAttributeTransfer = new CmsGlossaryAttributesTransfer();
        $glossaryAttributeTransfer->setPlaceholder($placeholder);
        $glossaryAttributeTransfer->setFkPage($idCmsPage);
        $glossaryAttributeTransfer->setTemplateName($templateName);
        $glossaryAttributeTransfer->setTranslations($translations);

        return $glossaryAttributeTransfer;
    }

    /**
     * @param array $translations
     *
     * @return \ArrayObject
     */
    protected function createCmsPlaceholderTranslationTransfers(array $translations)
    {
        $newTranslation = new ArrayObject();
        foreach ($translations as $localeName => $translation) {
            $localeTransfer = $this->localeFacade->getLocale($localeName);
            $newTranslation[] = $this->createCmsPlaceholderTranslationTransfer(
                $translation[SpyGlossaryTranslationTableMap::COL_VALUE],
                $localeTransfer
            );
        }

        return $newTranslation;
    }

    /**
     * @param string $value
     * @param \Generated\Shared\Transfer\LocaleTransfer $localeTransfer
     *
     * @return \Generated\Shared\Transfer\CmsPlaceholderTranslationTransfer
     */
    protected function createCmsPlaceholderTranslationTransfer($value, LocaleTransfer $localeTransfer)
    {
        $cmsPlaceholderTranslation = new CmsPlaceholderTranslationTransfer();
        $cmsPlaceholderTranslation->setTranslation($value);
        $cmsPlaceholderTranslation->setFkLocale($localeTransfer->getIdLocale());
        $cmsPlaceholderTranslation->setLocaleName($localeTransfer->getLocaleName());

        return $cmsPlaceholderTranslation;
    }

}
