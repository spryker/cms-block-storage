<?php

/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace Spryker\Zed\Product\Business;

use Spryker\Zed\Product\Business\Attribute\AttributeManager;
use Spryker\Zed\Product\Business\Model\ProductBatchResult;
use Spryker\Zed\Product\Business\Importer\Writer\Db\ProductConcreteWriter;
use Spryker\Zed\Product\Business\Importer\Writer\Db\ProductAbstractWriter;
use Spryker\Zed\Product\Business\Importer\Writer\ProductWriter;
use Spryker\Zed\Product\Business\Importer\Builder\ProductBuilder;
use Spryker\Zed\Product\Business\Importer\Reader\File\CsvReader;
use Spryker\Zed\Kernel\Business\AbstractBusinessFactory;
use Spryker\Zed\Product\Business\Importer\FileImporter;
use Spryker\Zed\Product\Business\Importer\Upload\UploadedFileImporter;
use Spryker\Zed\Product\Business\Importer\Validator\ImportProductValidator;
use Spryker\Zed\Product\Business\Internal\Install;
use Spryker\Zed\Messenger\Business\Model\MessengerInterface;
use Spryker\Zed\Product\ProductDependencyProvider;
use Spryker\Zed\Product\Business\Product\ProductManager;

/**
 * @method \Spryker\Zed\Product\ProductConfig getConfig()
 * @method \Spryker\Zed\Product\Persistence\ProductQueryContainerInterface getQueryContainer()
 */
class ProductBusinessFactory extends AbstractBusinessFactory
{

    /**
     * @var \Spryker\Zed\Product\Business\Product\ProductManager
     */
    protected $productManager;

    /**
     * @return \Spryker\Zed\Product\Business\Importer\Upload\UploadedFileImporter
     */
    public function createHttpFileImporter()
    {
        return new UploadedFileImporter(
            $this->getConfig()->getDestinationDirectoryForUploads()
        );
    }

    /**
     * @return string
     */
    public function getYvesUrl()
    {
        return $this->getConfig()->getHostYves();
    }

    /**
     * @return \Spryker\Zed\Product\Business\Importer\FileImporter
     */
    public function createProductImporter()
    {
        $importer = new FileImporter(
            $this->createImportProductValidator(),
            $this->createCSVReader(),
            $this->createImportProductBuilder(),
            $this->createProductWriter(),
            $this->createProductBatchResult()
        );

        return $importer;
    }

    /**
     * @return \Spryker\Zed\Product\Business\Importer\Validator\ImportProductValidator
     */
    protected function createImportProductValidator()
    {
        return new ImportProductValidator();
    }

    /**
     * @return \Spryker\Zed\Product\Business\Importer\Reader\File\IteratorReaderInterface
     */
    protected function createCSVReader()
    {
        return new CsvReader();
    }

    /**
     * @return \Spryker\Zed\Product\Business\Builder\ProductBuilderInterface
     */
    protected function createImportProductBuilder()
    {
        return new ProductBuilder();
    }

    /**
     * @return \Spryker\Zed\Product\Business\Importer\Writer\ProductWriterInterface
     */
    protected function createProductWriter()
    {
        return new ProductWriter(
            $this->createProductAbstractWriter(),
            $this->createProductConcreteWriter()
        );
    }

    /**
     * @return \Spryker\Zed\Product\Business\Importer\Writer\ProductAbstractWriterInterface
     */
    protected function createProductAbstractWriter()
    {
        return new ProductAbstractWriter(
            $this->getCurrentLocale()
        );
    }

    /**
     * @return \Spryker\Zed\Product\Business\Importer\Writer\ProductConcreteWriterInterface
     */
    protected function createProductConcreteWriter()
    {
        return new ProductConcreteWriter(
            $this->getCurrentLocale()
        );
    }

    /**
     * @return \Spryker\Zed\Product\Business\Model\ProductBatchResultInterface
     */
    protected function createProductBatchResult()
    {
        return new ProductBatchResult();
    }

    /**
     * @param \Spryker\Zed\Messenger\Business\Model\MessengerInterface $messenger
     *
     * @return \Spryker\Zed\Product\Business\Internal\Install
     */
    public function createInstaller(MessengerInterface $messenger)
    {
        $installer = new Install(
            $this->createAttributeManager()
        );
        $installer->setMessenger($messenger);

        return $installer;
    }

    /**
     * @return \Spryker\Zed\Product\Business\Attribute\AttributeManagerInterface
     */
    public function createAttributeManager()
    {
        return new AttributeManager(
            $this->getQueryContainer()
        );
    }

    /**
     * @return \Spryker\Zed\Product\Business\Product\ProductManagerInterface
     */
    public function createProductManager()
    {
        if ($this->productManager === null) {
            $this->productManager = new ProductManager(
                $this->getQueryContainer(),
                $this->getTouchFacade(),
                $this->getUrlFacade(),
                $this->getLocaleFacade()
            );
        }

        return $this->productManager;
    }

    /**
     * @return \Spryker\Zed\Product\Dependency\Facade\ProductToLocaleInterface
     */
    protected function getLocaleFacade()
    {
        return $this->getProvidedDependency(ProductDependencyProvider::FACADE_LOCALE);
    }

    /**
     * @return \Spryker\Zed\Product\Dependency\Facade\ProductToTouchInterface
     */
    protected function getTouchFacade()
    {
        return $this->getProvidedDependency(ProductDependencyProvider::FACADE_TOUCH);
    }

    /**
     * @return \Spryker\Zed\Product\Dependency\Facade\ProductToUrlInterface
     */
    protected function getUrlFacade()
    {
        return $this->getProvidedDependency(ProductDependencyProvider::FACADE_URL);
    }

    /**
     * @return \Generated\Shared\Transfer\LocaleTransfer
     */
    protected function getCurrentLocale()
    {
        return $this->getLocaleFacade()->getCurrentLocale();
    }

}
