<?php

/**
 * (c) Spryker Systems GmbH copyright protected.
 */

namespace Functional\SprykerFeature\Zed\Cms\Business;

use Codeception\TestCase\Test;
use Generated\Shared\Transfer\CmsTemplateTransfer;
use Generated\Shared\Transfer\PageKeyMappingTransfer;
use Generated\Shared\Transfer\PageTransfer;
use Generated\Zed\Ide\AutoCompletion;
use SprykerEngine\Zed\Kernel\Container;
use SprykerEngine\Zed\Locale\Business\LocaleFacade;
use SprykerFeature\Zed\Cms\Business\CmsFacade;
use SprykerFeature\Zed\Cms\CmsDependencyProvider;
use SprykerFeature\Zed\Cms\Persistence\CmsQueryContainer;
use SprykerFeature\Zed\Cms\Persistence\CmsQueryContainerInterface;
use SprykerFeature\Zed\Glossary\Business\GlossaryFacade;
use SprykerFeature\Zed\Glossary\GlossaryDependencyProvider;
use SprykerFeature\Zed\Glossary\Persistence\GlossaryQueryContainer;
use SprykerFeature\Zed\Glossary\Persistence\GlossaryQueryContainerInterface;
use SprykerFeature\Zed\Url\Business\UrlFacade;

class CmsFacadeTest extends Test
{

    /**
     * @var CmsFacade
     */
    protected $cmsFacade;

    /**
     * @var CmsQueryContainerInterface
     */
    protected $cmsQueryContainer;

    /**
     * @var GlossaryFacade
     */
    protected $glossaryFacade;

    /**
     * @var UrlFacade
     */
    protected $urlFacade;

    /**
     * @var LocaleFacade
     */
    protected $localeFacade;

    /**
     * @var GlossaryQueryContainerInterface
     */
    protected $glossaryQueryContainer;

    /**
     * @var AutoCompletion
     */
    protected $locator;

    /**
     * @return void
     */
    protected function setUp()
    {
        parent::setUp();

        $container = new Container();
        $container = (new CmsDependencyProvider())
            ->provideBusinessLayerDependencies($container);

        $this->cmsFacade = new CmsFacade();
        $this->cmsFacade->setExternalDependencies($container);

        $this->urlFacade = new UrlFacade();

        $this->localeFacade = new LocaleFacade();
        $this->cmsQueryContainer = new CmsQueryContainer();
        $this->glossaryQueryContainer = new GlossaryQueryContainer();

        $this->buildGlossaryFacade();
    }

    /**
     * @group Cms
     *
     * @return void
     */
    public function testCreateTemplateInsertsAndReturnsSomething()
    {
        $templateQuery = $this->cmsQueryContainer->queryTemplates();

        $templateCountBeforeCreation = $templateQuery->count();
        $newTemplate = $this->cmsFacade->createTemplate('ATemplateName', 'ATemplatePath');
        $templateCountAfterCreation = $templateQuery->count();

        $this->assertTrue($templateCountAfterCreation > $templateCountBeforeCreation);

        $this->assertNotNull($newTemplate->getIdCmsTemplate());
    }

    /**
     * @group Cms
     *
     * @return void
     */
    public function testSavePageInsertsAndReturnsSomethingOnCreate()
    {
        $pageQuery = $this->cmsQueryContainer->queryPages();
        $this->localeFacade->createLocale('ABCDE');

        $template = $this->cmsFacade->createTemplate('AUsedTemplateName', 'AUsedTemplatePath');

        $page = new PageTransfer();
        $page->setFkTemplate($template->getIdCmsTemplate());
        $page->setIsActive(true);

        $pageCountBeforeCreation = $pageQuery->count();
        $page = $this->cmsFacade->savePage($page);
        $pageCountAfterCreation = $pageQuery->count();

        $this->assertTrue($pageCountAfterCreation > $pageCountBeforeCreation);

        $this->assertNotNull($page->getIdCmsPage());
    }

    /**
     * @group Cms
     *
     * @return void
     */
    public function testSavePageWithNewTemplateMustSaveFkTemplateInPage()
    {
        $template1 = $this->cmsFacade->createTemplate('AnotherUsedTemplateName', 'AnotherUsedTemplatePath');
        $template2 = $this->cmsFacade->createTemplate('YetAnotherUsedTemplateName', 'YetAnotherUsedTemplatePath');

        $pageTransfer = new PageTransfer();
        $pageTransfer->setFkTemplate($template1->getIdCmsTemplate());
        $pageTransfer->setIsActive(true);

        $pageTransfer = $this->cmsFacade->savePage($pageTransfer);

        $pageEntity = $this->cmsQueryContainer->queryPageById($pageTransfer->getIdCmsPage())
            ->findOne();
        $this->assertEquals($template1->getIdCmsTemplate(), $pageEntity->getFkTemplate());

        $pageTransfer->setFkTemplate($template2->getIdCmsTemplate());
        $this->cmsFacade->savePage($pageTransfer);

        $pageEntity = $this->cmsQueryContainer->queryPageById($pageTransfer->getIdCmsPage())
            ->findOne();

        $this->assertEquals($template2->getIdCmsTemplate(), $pageEntity->getFkTemplate());
    }

    /**
     * @group Cms
     *
     * @return void
     */
    public function testSaveTemplateInsertsAndReturnsSomethingOnCreate()
    {
        $template = new CmsTemplateTransfer();
        $template->setTemplateName('WhatARandomName');
        $template->setTemplatePath('WhatARandomPath');

        $templateQuery = $this->cmsQueryContainer->queryTemplates();

        $templateCountBeforeCreation = $templateQuery->count();
        $template = $this->cmsFacade->saveTemplate($template);
        $templateCountAfterCreation = $templateQuery->count();

        $this->assertTrue($templateCountAfterCreation > $templateCountBeforeCreation);

        $this->assertNotNull($template->getIdCmsTemplate());
    }

    /**
     * @group Cms
     *
     * @return void
     */
    public function testSaveTemplateUpdatesSomething()
    {
        $template = new CmsTemplateTransfer();
        $template->setTemplateName('WhatARandomName');
        $template->setTemplatePath('WhatARandomPath2');
        $template = $this->cmsFacade->saveTemplate($template);

        $templateQuery = $this->cmsQueryContainer->queryTemplateById($template->getIdCmsTemplate());

        $this->assertEquals('WhatARandomPath2', $templateQuery->findOne()
            ->getTemplatePath());

        $template->setTemplatePath('WhatAnotherRandomPath2');
        $this->cmsFacade->saveTemplate($template);

        $this->assertEquals('WhatAnotherRandomPath2', $templateQuery->findOne()
            ->getTemplatePath());
    }

    /**
     * @group Cms
     *
     * @return void
     */
    public function testSavePageKeyMappingInsertsAndReturnsSomethingOnCreate()
    {
        $pageKeyMappingQuery = $this->cmsQueryContainer->queryGlossaryKeyMappings();

        $glossaryKeyId = $this->glossaryFacade->createKey('AHopefullyNotYetExistingKey');
        $template = $this->cmsFacade->createTemplate('ANotExistingTemplateName', 'ANotYetExistingTemplatePath');

        $page = new PageTransfer();
        $page->setFkTemplate($template->getIdCmsTemplate());
        $page->setIsActive(true);

        $page = $this->cmsFacade->savePage($page);

        $pageKeyMapping = new PageKeyMappingTransfer();
        $pageKeyMapping->setFkGlossaryKey($glossaryKeyId);
        $pageKeyMapping->setFkPage($page->getIdCmsPage());
        $pageKeyMapping->setPlaceholder('SomePlaceholderName');

        $mappingCountBeforeCreation = $pageKeyMappingQuery->count();
        $pageKeyMapping = $this->cmsFacade->savePageKeyMapping($pageKeyMapping);
        $mappingCountAfterCreation = $pageKeyMappingQuery->count();

        $this->assertTrue($mappingCountAfterCreation > $mappingCountBeforeCreation);

        $this->assertNotNull($pageKeyMapping->getIdCmsGlossaryKeyMapping());
    }

    /**
     * @group Cms
     *
     * @return void
     */
    public function testSavePageKeyMappingUpdatesSomething()
    {
        $glossaryKeyId1 = $this->glossaryFacade->createKey('AHopefullyNotYetExistingKey2');
        $glossaryKeyId2 = $this->glossaryFacade->createKey('AHopefullyNotYetExistingKey3');
        $template = $this->cmsFacade->createTemplate('ANotExistingTemplateName2', 'ANotYetExistingTemplatePath2');

        $page = new PageTransfer();
        $page->setFkTemplate($template->getIdCmsTemplate());
        $page->setIsActive(true);

        $page = $this->cmsFacade->savePage($page);

        $pageKeyMapping = new PageKeyMappingTransfer();
        $pageKeyMapping->setFkGlossaryKey($glossaryKeyId1);
        $pageKeyMapping->setFkPage($page->getIdCmsPage());
        $pageKeyMapping->setPlaceholder('SomePlaceholderName');

        $pageKeyMapping = $this->cmsFacade->savePageKeyMapping($pageKeyMapping);

        $pageKeyMappingQuery = $this->cmsQueryContainer->queryGlossaryKeyMappingById($pageKeyMapping->getIdCmsGlossaryKeyMapping());

        $this->assertEquals($glossaryKeyId1, $pageKeyMappingQuery->findOne()
            ->getFkGlossaryKey());

        $pageKeyMapping->setFkGlossaryKey($glossaryKeyId2);
        $this->cmsFacade->savePageKeyMapping($pageKeyMapping);

        $this->assertEquals($glossaryKeyId2, $pageKeyMappingQuery->findOne()
            ->getFkGlossaryKey());
    }

    /**
     * @group Cms
     *
     * @return void
     */
    public function testAddPlaceholderTextInsertsAndReturnsSomething()
    {
        $keyQuery = $this->glossaryQueryContainer->queryKeys();
        $pageMappingQuery = $this->cmsQueryContainer->queryGlossaryKeyMappings();

        $template = $this->cmsFacade->createTemplate('APlaceholderTemplate', 'APlaceholderTemplatePath');

        $page = new PageTransfer();
        $page->setFkTemplate($template->getIdCmsTemplate());
        $page->setIsActive(true);

        $page = $this->cmsFacade->savePage($page);

        $keyCountBeforeCreation = $keyQuery->count();
        $mappingCountBeforeCreation = $pageMappingQuery->count();

        $mapping = $this->cmsFacade->addPlaceholderText($page, 'Placeholder1', 'Some Translation');

        $keyCountAfterCreation = $keyQuery->count();
        $mappingCountAfterCreation = $pageMappingQuery->count();

        $this->assertTrue($keyCountAfterCreation > $keyCountBeforeCreation);
        $this->assertTrue($mappingCountAfterCreation > $mappingCountBeforeCreation);

        $this->assertNotNull($mapping->getIdCmsGlossaryKeyMapping());
    }

    /**
     * @group Cms
     *
     * @return void
     */
    public function testTranslatePlaceholder()
    {
        $template = $this->cmsFacade->createTemplate('APlaceholderTemplate2', 'APlaceholderTemplatePath2');

        $page = new PageTransfer();
        $page->setFkTemplate($template->getIdCmsTemplate());
        $page->setIsActive(true);

        $page = $this->cmsFacade->savePage($page);
        $this->cmsFacade->addPlaceholderText($page, 'Placeholder1', 'A Placeholder Translation');

        $translation = $this->cmsFacade->translatePlaceholder($page->getIdCmsPage(), 'Placeholder1');
        $this->assertEquals('A Placeholder Translation', $translation);
    }

    /**
     * @return void
     */
    protected function buildGlossaryFacade()
    {
        $this->glossaryFacade = new GlossaryFacade();
        $container = new Container();

        $container[GlossaryDependencyProvider::FACADE_LOCALE] = function (Container $container) {
            return $this->localeFacade;
        };

        $this->glossaryFacade->setExternalDependencies($container);
        $this->glossaryFacade->setOwnQueryContainer($this->glossaryQueryContainer);
    }

}
