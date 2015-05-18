<?php

namespace SprykerEngine\Zed\Transfer\Business\Model\Generator\Transfer;

use SprykerEngine\Zed\Transfer\Business\Model\Generator\DefinitionInterface;
use SprykerEngine\Zed\Transfer\Business\Model\Generator\GeneratorInterface;
use SprykerEngine\Zed\Transfer\Business\Model\Generator\TransferTwigExtensions;

class ClassGenerator implements GeneratorInterface
{

    const TWIG_TEMPLATES_LOCATION = '/../Templates/';

    /**
     * @var string
     */
    protected $targetDirectory = null;

    /**
     * @var \Twig_Environment
     */
    protected $twig;

    /**
     * @param $targetDirectory
     */
    public function __construct($targetDirectory)
    {
        $this->targetDirectory = $targetDirectory;

        \Twig_Autoloader::register();

        $loader = new \Twig_Loader_Filesystem(__DIR__ . self::TWIG_TEMPLATES_LOCATION);
        $this->twig = new \Twig_Environment($loader, []);
        $this->twig->addExtension(new TransferTwigExtensions());
    }

    /**
     * @param DefinitionInterface $definition
     *
     * @return string
     */
    public function generate(DefinitionInterface $definition)
    {
        $twigData = $this->getTwigData($definition);
        $fileName = $definition->getName() . '.php';
        $fileContent = $this->twig->render('class.php.twig', $twigData);

        if (!is_dir($this->targetDirectory)) {
            mkdir($this->targetDirectory, 0755, true);
        }

        file_put_contents($this->targetDirectory . $fileName, $fileContent);

        return $fileName;
    }

    /**
     * @param ClassDefinitionInterface $classDefinition
     *
     * @return array
     */
    public function getTwigData(ClassDefinitionInterface $classDefinition)
    {
        return [
            'className' => $classDefinition->getName(),
            'uses' => $classDefinition->getUses(),
            'interfaces' => $classDefinition->getInterfaces(),
            'constructorDefinition' => $classDefinition->getConstructorDefinition(),
            'properties' => $classDefinition->getProperties(),
            'methods' => $classDefinition->getMethods()
        ];
    }

}
