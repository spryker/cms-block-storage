<?php

namespace SprykerEngine\Zed\Transfer\Business\Model;

use SprykerEngine\Zed\Transfer\Business\Model\Generator\DefinitionBuilderInterface;
use SprykerEngine\Zed\Transfer\Business\Model\Generator\GeneratorInterface;
use Psr\Log\LoggerInterface;

class TransferGenerator
{

    /**
     * @var GeneratorInterface
     */
    private $generator;

    /**
     * @var DefinitionBuilderInterface
     */
    private $definitionBuilder;

    /**
     * @param LoggerInterface $messenger
     * @param GeneratorInterface $generator
     * @param DefinitionBuilderInterface $definitionBuilder
     */
    public function __construct(LoggerInterface $messenger, GeneratorInterface $generator, DefinitionBuilderInterface $definitionBuilder)
    {
        $this->messenger = $messenger;
        $this->generator = $generator;
        $this->definitionBuilder = $definitionBuilder;
    }

    public function execute()
    {
        $definitions = $this->definitionBuilder->getDefinitions();

        foreach ($definitions as $classDefinition) {
            $fileName = $this->generator->generate($classDefinition);

            $this->messenger->info(
                'transfer.generated',
                ['%name%' => $fileName,]
            );
        }
    }

}
