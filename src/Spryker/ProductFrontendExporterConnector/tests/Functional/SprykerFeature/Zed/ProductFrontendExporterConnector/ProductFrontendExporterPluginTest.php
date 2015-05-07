<?php

namespace Functional\SprykerFeature\Zed\ProductFrontendExporterConnector;

use Codeception\TestCase\Test;
use Generated\Shared\Transfer\LocaleTransfer;
use Generated\Zed\Ide\AutoCompletion;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\Exception\PropelException;
use Pyz\Zed\Locale\Business\LocaleFacade;
use Pyz\Zed\Product\Business\ProductFacade;
use Pyz\Zed\Touch\Business\TouchFacade;
use SprykerEngine\Zed\Kernel\Locator;
use SprykerEngine\Zed\Touch\Persistence\Propel\Map\SpyTouchTableMap;
use SprykerEngine\Zed\Touch\Persistence\Propel\SpyTouchQuery;
use SprykerFeature\Zed\Category\Business\CategoryFacade;
use SprykerFeature\Zed\FrontendExporter\Dependency\Plugin\DataProcessorPluginInterface;
use SprykerFeature\Zed\FrontendExporter\Dependency\Plugin\QueryExpanderPluginInterface;
use SprykerFeature\Zed\Library\Propel\Formatter\PropelArraySetFormatter;
use SprykerFeature\Zed\Url\Business\UrlFacade;

/**
 * @group SprykerFeature
 * @group Zed
 * @group ProductFrontendExporterConnector
 * @group ProductFrontendExporterPluginTest
 * @group FrontendExporterPlugin
 */
class ProductFrontendExporterPluginTest extends Test
{
    /**
     * @var AutoCompletion
     */
    protected $locator;

    /**
     * @var LocaleFacade
     */
    protected $localeFacade;

    /**
     * @var ProductFacade
     */
    protected $productFacade;

    /**
     * @var CategoryFacade
     */
    protected $categoryFacade;

    /**
     * @var TouchFacade
     */
    protected $touchFacade;

    /**
     * @var UrlFacade
     */
    protected $urlFacade;



    /**
     * @var LocaleTransfer
     */
    protected $locale;

    /**
     * @group Exporter
     */
    public function testSoleProductExporter()
    {
        $this->createAttributeType();
        $idAbstractProduct = $this->createProduct('TestSku', 'TestProductName', $this->locale);
        $this->urlFacade->createUrl('/some-url', $this->locale, 'abstract_product', $idAbstractProduct);
        $this->touchFacade->touchActive('test', $idAbstractProduct);

        $this->doExporterTest(
            [   //expanders
                $this->locator->productFrontendExporterConnector()->pluginProductQueryExpanderPlugin()
            ],
            [   //processors
                $this->locator->productFrontendExporterConnector()->pluginProductProcessorPlugin()
            ],
            [
                'de.abcde.resource.abstract_product.' . $idAbstractProduct =>
                    [
                        'sku' => 'AbstractTestSku',
                        'abstract_attributes' =>
                            [
                                'thumbnail_url' => '/images/product/default.png',
                                'price' => 1395,
                                'width' => 12,
                                'height' => 27,
                                'depth' => 850,
                                'main_color' => 'gray',
                                'other_colors' => 'red',
                                'description' => 'A description!',
                                'name' => 'Ted Technical Robot',
                            ],
                        'name' => 'TestProductName',
                        'url' => '/some-url',
                        'concrete_products' => [
                            [
                                'sku' => 'TestSku',
                                'attributes' => [
                                    'image_url' => '/images/product/robot_buttons_black.png',
                                    'weight' => 1.2,
                                    'material' => 'aluminium',
                                    'gender' => 'b',
                                    'age' => 8,
                                    'available' => true,
                                ]
                            ]
                        ]
                    ]
            ]
        );
    }

    protected function createAttributeType()
    {
        if (!$this->productFacade->hasAttributeType('test')) {
            $this->productFacade->createAttributeType('test', 'test');
        }
    }

    /**
     * @param string $sku
     * @param string $name
     * @param LocaleTransfer $locale
     *
     * @return int
     */
    protected function createProduct($sku, $name, LocaleTransfer $locale)
    {
        $idAbstractProduct = $this->createAbstractProductWithAttributes('Abstract' . $sku, 'Abstract' . $name, $locale);
        $this->createConcreteProductWithAttributes($idAbstractProduct, $sku, $name, $locale);

        return $idAbstractProduct;
    }

    /**
     * @param string $sku
     * @param string $name
     * @param LocaleTransfer $locale
     *
     * @return int
     */
    protected function createAbstractProductWithAttributes($sku, $name, $locale)
    {
        $idAbstractProduct = $this->productFacade->createAbstractProduct($sku);

        $this->productFacade->createAbstractProductAttributes(
            $idAbstractProduct,
            $locale,
            $name,
            json_encode(
                [
                    'thumbnail_url' => '/images/product/default.png',
                    'price' => 1395,
                    'width' => 12,
                    'height' => 27,
                    'depth' => 850,
                    'main_color' => 'gray',
                    'other_colors' => 'red',
                    'description' => 'A description!',
                    'name' => 'Ted Technical Robot',
                ]
            )
        );

        return $idAbstractProduct;
    }

    /**
     * @param int $idAbstractProduct
     * @param string $sku
     * @param string $name
     * @param LocaleTransfer $locale
     *
     * @return int
     */
    protected function createConcreteProductWithAttributes($idAbstractProduct, $sku, $name, LocaleTransfer $locale)
    {
        $idConcreteProduct = $this->productFacade->createConcreteProduct($sku, $idAbstractProduct, true);

        $this->productFacade->createConcreteProductAttributes(
            $idConcreteProduct,
            $locale,
            $name,
            json_encode(
                [
                    'image_url' => '/images/product/robot_buttons_black.png',
                    'weight' => 1.2,
                    'material' => 'aluminium',
                    'gender' => 'b',
                    'age' => 8,
                    'available' => true,
                ]
            )
        );

        return $idConcreteProduct;
    }

    /**
     * @param QueryExpanderPluginInterface[] $expanderCollection
     * @param DataProcessorPluginInterface[] $processors
     * @param array $expectedResult
     */
    public function doExporterTest(array $expanderCollection, array $processors, array $expectedResult)
    {
        $query = $this->prepareQuery();

        foreach ($expanderCollection as $expander) {
            $query = $expander->expandQuery($query, $this->locale);
        }

        $results = $query->find();

        $processedResultSet = [];
        foreach ($processors as $processor) {
            $processedResultSet = $processor->processData($results, $processedResultSet, $this->locale);
        }

        $this->assertEquals($expectedResult, $processedResultSet);
    }

    /**
     * @return ModelCriteria
     * @throws PropelException
     */
    protected function prepareQuery()
    {
        $query = SpyTouchQuery::create()
            ->filterByItemEvent(SpyTouchTableMap::COL_ITEM_EVENT_ACTIVE)
            ->setFormatter(new PropelArraySetFormatter())
            ->filterByItemType('test');

        return $query;
    }

    protected function setUp()
    {
        parent::setUp();
        $this->locator = Locator::getInstance();
        $this->localeFacade = $this->locator->locale()->facade();
        $this->productFacade = $this->locator->product()->facade();
        $this->categoryFacade = $this->locator->category()->facade();
        $this->touchFacade = $this->locator->touch()->facade();
        $this->urlFacade = $this->locator->url()->facade();
        $this->locale = $this->localeFacade->createLocale('ABCDE');
    }
}
