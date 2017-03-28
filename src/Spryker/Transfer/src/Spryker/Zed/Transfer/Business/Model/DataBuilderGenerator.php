<?php
namespace Spryker\Zed\Transfer\Business\Model;

use Spryker\Zed\Transfer\Business\Model\Generator\DefinitionBuilderInterface;
use Spryker\Zed\Transfer\Business\Model\Generator\GeneratorInterface;

class DataBuilderGenerator
{
    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $messenger;

    /**
     * @var \Spryker\Zed\Transfer\Business\Model\Generator\GeneratorInterface
     */
    private $generator;

    /**
     * @var \Spryker\Zed\Transfer\Business\Model\Generator\DefinitionBuilderInterface
     */
    private $definitionBuilder;

    /**
     * @param \Psr\Log\LoggerInterface $messenger
     * @param \Spryker\Zed\Transfer\Business\Model\Generator\GeneratorInterface $generator
     * @param \Spryker\Zed\Transfer\Business\Model\Generator\DefinitionBuilderInterface $definitionBuilder
     */
    public function __construct(LoggerInterface $messenger, GeneratorInterface $generator, DefinitionBuilderInterface $definitionBuilder)
    {
        $this->messenger = $messenger;
        $this->generator = $generator;
        $this->definitionBuilder = $definitionBuilder;
    }

    /**
     * @return void
     */
    public function execute()
    {
        $definitions = $this->definitionBuilder->getDefinitions();

        foreach ($definitions as $classDefinition) {
            $fileName = $this->generator->generate($classDefinition);

            $this->messenger->info(sprintf('<info>%s</info> was generated', $fileName));
        }
    }


}