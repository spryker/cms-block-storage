<?php

/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace Spryker\Zed\Transfer\Business;

use Spryker\Zed\Messenger\Business\Model\MessengerInterface;
use Spryker\Zed\Transfer\Business\Model\Generator\DefinitionNormalizer;
use Spryker\Zed\Transfer\Business\Model\Generator\Transfer\ClassDefinition;
use Spryker\Zed\Transfer\Business\Model\Generator\TransferDefinitionMerger;
use Spryker\Zed\Kernel\Business\AbstractBusinessFactory;
use Spryker\Zed\Transfer\Business\Model\Generator\DefinitionBuilderInterface;
use Spryker\Zed\Transfer\Business\Model\Generator\Transfer\ClassGenerator;
use Spryker\Zed\Transfer\Business\Model\Generator\Transfer\TransferDefinitionBuilder;
use Spryker\Zed\Transfer\Business\Model\Generator\TransferDefinitionLoader;
use Spryker\Zed\Transfer\Business\Model\TransferCleaner;
use Spryker\Zed\Transfer\Business\Model\TransferGenerator;
use Spryker\Zed\Transfer\TransferConfig;

/**
 * @method TransferConfig getConfig()
 */
class TransferBusinessFactory extends AbstractBusinessFactory
{

    /**
     * @param \Spryker\Zed\Messenger\Business\Model\MessengerInterface $messenger
     *
     * @return \Spryker\Zed\Transfer\Business\Model\TransferGenerator
     */
    public function createTransferGenerator(MessengerInterface $messenger)
    {
        return new TransferGenerator(
            $messenger,
            $this->createClassGenerator(),
            $this->createTransferDefinitionBuilder()
        );
    }

    /**
     * @return \Spryker\Zed\Transfer\Business\Model\Generator\Transfer\ClassGenerator
     */
    protected function createClassGenerator()
    {
        return new ClassGenerator(
            $this->getConfig()->getClassTargetDirectory()
        );
    }

    /**
     * @return TransferDefinitionBuilder|DefinitionBuilderInterface
     */
    protected function createTransferDefinitionBuilder()
    {
        return new TransferDefinitionBuilder(
            $this->createLoader(),
            $this->createTransferDefinitionMerger(),
            $this->createClassDefinition()
        );
    }

    /**
     * @return \Spryker\Zed\Transfer\Business\Model\Generator\TransferDefinitionLoader
     */
    protected function createLoader()
    {
        return new TransferDefinitionLoader(
            $this->createDefinitionNormalizer(),
            $this->getConfig()->getSourceDirectories()
        );
    }

    /**
     * @return \Spryker\Zed\Transfer\Business\Model\TransferCleaner
     */
    public function createTransferCleaner()
    {
        return new TransferCleaner(
            $this->getConfig()->getGeneratedTargetDirectory()
        );
    }

    /**
     * @return \Spryker\Zed\Transfer\Business\Model\Generator\TransferDefinitionMerger
     */
    protected function createTransferDefinitionMerger()
    {
        return new TransferDefinitionMerger();
    }

    /**
     * @return \Spryker\Zed\Transfer\Business\Model\Generator\Transfer\ClassDefinition
     */
    protected function createClassDefinition()
    {
        return new ClassDefinition();
    }

    /**
     * @return \Spryker\Zed\Transfer\Business\Model\Generator\DefinitionNormalizer
     */
    protected function createDefinitionNormalizer()
    {
        return new DefinitionNormalizer();
    }

}
