<?php

namespace Functional\SprykerFeature\Zed\GlossaryQueue\Communication\Plugin;

use Codeception\TestCase\Test;
use Generated\Shared\Transfer\LocaleTransfer;
use Generated\Shared\Transfer\QueueMessageTransfer;
use Generated\Zed\Ide\AutoCompletion;
use SprykerFeature\Zed\Glossary\Business\GlossaryDependencyContainer;
use SprykerFeature\Zed\Glossary\Business\GlossaryFacade;
use SprykerFeature\Zed\Glossary\GlossaryDependencyProvider;
use SprykerEngine\Zed\Kernel\Container;
use SprykerEngine\Zed\Kernel\Locator;
use SprykerEngine\Zed\Locale\Business\LocaleFacade;
use SprykerFeature\Zed\Glossary\Persistence\GlossaryQueryContainer;
use Orm\Zed\Glossary\Persistence\Base\SpyGlossaryKeyQuery;
use Orm\Zed\Glossary\Persistence\Base\SpyGlossaryTranslationQuery;
use SprykerFeature\Zed\GlossaryQueue\Business\GlossaryQueueFacade;
use SprykerFeature\Zed\GlossaryQueue\Communication\Plugin\GlossaryTaskWorkerPlugin;
use SprykerFeature\Zed\GlossaryQueue\GlossaryQueueDependencyProvider;

/**
 * @group SprykerFeature
 * @group Zed
 * @group GlossaryQueue
 * @group Communication
 * @group GlossaryTaskWorkerPlugin
 */
class GlossaryTaskWorkerPluginTest extends Test
{

    /**
     * @var GlossaryTaskWorkerPlugin
     */
    protected $taskPlugin;

    /**
     * @var GlossaryFacade
     */
    protected $glossaryFacade;

    /**
     * @var array
     */
    private $locales = [];

    /**
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->generateTestLocales();

        $this->glossaryFacade = $this->createGlossaryFacade();
        $this->taskPlugin = $this->createGlossaryTaskWorkerPlugin();
    }

    /**
     * @return void
     */
    private function generateTestLocales()
    {
        $locale = $this->getLocaleFacade()->createLocale('xx_XX');
        $this->locales[$locale->getIdLocale()] = $locale->getLocaleName();
    }

    /**
     * @return LocaleFacade
     */
    private function getLocaleFacade()
    {
        return new LocaleFacade();
    }

    /**
     * @return GlossaryQueueFacade
     */
    private function createGlossaryQueueFacade()
    {
        $container = new Container();
        $container[GlossaryQueueDependencyProvider::GLOSSARY_FACADE] = function () {
            return $this->glossaryFacade;
        };

        $glossaryQueueFacade = new GlossaryQueueFacade();
        $glossaryQueueFacade->setExternalDependencies($container);

        return $glossaryQueueFacade;
    }

    /**
     * @return GlossaryQueueFacade
     */
    private function createGlossaryFacade()
    {
        $provider = new GlossaryDependencyProvider();
        $glossaryFacade = new MockGlossaryFacade();
        $glossaryFacade->setExternalDependencies($provider->provideBusinessLayerDependencies(new Container()));
        $glossaryFacade->setOwnQueryContainer(
            new GlossaryQueryContainer()
        );

        $glossaryFacade->setDependencyContainer(new GlossaryDependencyContainer());

        return $glossaryFacade;
    }

    /**
     * @return AutoCompletion|Locator
     */
    private function getLocator()
    {
        return Locator::getInstance();
    }

    /**
     * @return void
     */
    public function testPluginShouldCreateKeyAndTranslation()
    {
        $queueMessage = $this->getQueueMessage()->setPayload(
            [
                'translation_key' => 'test.key1',
                'translation_value' => 'test.value1',
                'translation_is_active' => true,
                'translation_locale' => 'xx_XX',
            ]
        );
        $this->taskPlugin->run($queueMessage);

        $keyResult = SpyGlossaryKeyQuery::create()->filterByKey('test.key1')->count();

        $translationResult = SpyGlossaryTranslationQuery::create()
            ->filterByValue('test.value1')
            ->filterByIsActive(true)
            ->useLocaleQuery()
            ->filterByLocaleName('xx_XX')
            ->endUse()
            ->count();

        $this->assertEquals(1, $keyResult);
        $this->assertEquals(1, $translationResult);
    }

    /**
     * @return void
     */
    public function testShouldUpdateTranslation()
    {
        $idGlossaryKey = $this->glossaryFacade->createKey('test.key2');
        $this->glossaryFacade->createTranslation(
            'test.key2',
            (new LocaleTransfer())->setLocaleName('xx_XX'),
            'test.value2',
            true
        );

        $queueMessage = $this->getQueueMessage()->setPayload(
            [
                'translation_key' => 'test.key2',
                'translation_value' => 'test.updated.value2',
                'translation_is_active' => false,
                'translation_locale' => 'xx_XX',
            ]
        );
        $this->taskPlugin->run($queueMessage);

        $updatedTranslationCount = SpyGlossaryTranslationQuery::create()
            ->filterByFkGlossaryKey($idGlossaryKey)
            ->filterByValue('test.updated.value2')
            ->filterByIsActive(false)
            ->useLocaleQuery()
            ->filterByLocaleName('xx_XX')
            ->endUse()
            ->count();

        $this->assertEquals(1, $updatedTranslationCount);
    }

    /**
     * @return QueueMessageTransfer
     */
    protected function getQueueMessage()
    {
        return new QueueMessageTransfer();
    }

    /**
     * @return GlossaryTaskWorkerPlugin
     */
    private function createGlossaryTaskWorkerPlugin()
    {
        $glossaryTaskWorkerPluginMock = $this->getMock(GlossaryTaskWorkerPlugin::class, ['getFacade']);

        $glossaryTaskWorkerPluginMock->method('getFacade')->willReturn($this->createGlossaryQueueFacade());

        return $glossaryTaskWorkerPluginMock;
    }

}
