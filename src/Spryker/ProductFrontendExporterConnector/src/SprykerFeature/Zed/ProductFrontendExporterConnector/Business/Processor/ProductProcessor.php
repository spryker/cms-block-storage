<?php

namespace SprykerFeature\Zed\ProductFrontendExporterConnector\Business\Processor;

use SprykerEngine\Shared\Locale\Dto\LocaleDto;
use SprykerFeature\Shared\FrontendExporter\Code\KeyBuilder\KeyBuilderInterface;
use SprykerFeature\Zed\ProductFrontendExporterConnector\Dependency\Facade\ProductFrontendExporterToProductInterface;

class ProductProcessor implements ProductProcessorInterface
{

    /**
     * @var ProductFrontendExporterToProductInterface
     */
    protected $productBuilder;
    /**
     * @var KeyBuilderInterface
     */
    private $productKeyGenerator;

    /**
     * @param ProductFrontendExporterToProductInterface $productBuilder
     * @param KeyBuilderInterface $productKeyBuilder
     */
    public function __construct(
        ProductFrontendExporterToProductInterface $productBuilder,
        KeyBuilderInterface $productKeyBuilder
    ) {
        $this->productBuilder = $productBuilder;
        $this->productKeyGenerator = $productKeyBuilder;
    }

    /**
     * @param array $products
     * @param LocaleDto $locale
     *
     * @return array
     */
    public function buildProducts(array $products, LocaleDto $locale)
    {
        $products = $this->productBuilder->buildProducts($products);

        $exportChunk = [];

        foreach ($products as $index => $productData) {
            $productKey = $this->productKeyGenerator->generateKey($productData['id_abstract_product'], $locale->getLocaleName());
            $productData['url'] = $productData['product_url'];
            $exportChunk[$productKey] = $this->filterProductData($productData);
        }

        return $exportChunk;
    }

    /**
     * @param array $productData
     *
     * @return array
     */
    protected function filterProductData(array $productData)
    {
        //TODO get this from the settings, instead of hardcoding it
        $allowedFields = [
            'abstract_sku' => true,
            'abstract_attributes' => true,
            'name' => true,
            'url' => true,
            'concrete_products' => true,
        ];

        return array_intersect_key($productData, $allowedFields);
    }
}
