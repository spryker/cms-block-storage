<?php

/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace Spryker\Zed\Setup\Communication\Console;

use Spryker\Zed\Kernel\IdeAutoCompletion\MethodTagBuilder\ClientMethodTagBuilder;
use Spryker\Shared\Config;
use Spryker\Shared\Application\ApplicationConstants;
use Spryker\Zed\Console\Business\Model\Console;
use Spryker\Zed\Kernel\BundleNameFinder;
use Spryker\Zed\Kernel\IdeAutoCompletion\IdeAutoCompletionGenerator;
use Spryker\Zed\Kernel\IdeAutoCompletion\IdeBundleAutoCompletionGenerator;
use Spryker\Zed\Kernel\IdeAutoCompletion\MethodTagBuilder\GeneratedInterfaceMethodTagBuilder;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateClientIdeAutoCompletionConsole extends Console
{

    const COMMAND_NAME = 'setup:generate-client-ide-auto-completion';

    /**
     * @return void
     */
    protected function configure()
    {
        $this->setName(self::COMMAND_NAME);
        $this->setDescription('Generate the bundle ide auto completion interface for the Client.');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->generateClientInterface();
        $this->generateClientBundleInterface();
    }

    /**
     * @return void
     */
    protected function generateClientInterface()
    {
        $options = $this->getClientDefaultOptions();

        $generator = new IdeAutoCompletionGenerator($options, $this);
        $generator
            ->addMethodTagBuilder(new GeneratedInterfaceMethodTagBuilder(
                [
                    GeneratedInterfaceMethodTagBuilder::OPTION_METHOD_STRING_PATTERN => ' * @method \\Generated\Client\Ide\{{bundle}} {{methodName}}()',
                ]
            ));
        $generator->create();

        $this->info('Generated Client IdeAutoCompletion file');
    }

    /**
     * @return array
     */
    protected function getClientDefaultOptions()
    {
        $options = [
            IdeAutoCompletionGenerator::OPTION_KEY_NAMESPACE => 'Generated\Client\Ide',
            IdeAutoCompletionGenerator::OPTION_KEY_LOCATION_DIR => APPLICATION_SOURCE_DIR . '/Generated/Client/Ide/',
            IdeAutoCompletionGenerator::OPTION_KEY_APPLICATION => 'Client',
            IdeAutoCompletionGenerator::OPTION_KEY_BUNDLE_NAME_FINDER => new BundleNameFinder(
                [
                    BundleNameFinder::OPTION_KEY_APPLICATION => 'Client',
                    BundleNameFinder::OPTION_KEY_BUNDLE_PROJECT_PATH_PATTERN => $this->getProjectNamespace() . '/',
                ]
            ),
        ];

        return $options;
    }

    /**
     * @return void
     */
    protected function generateClientBundleInterface()
    {
        $options = $this->getClientDefaultOptions();
        $options[IdeBundleAutoCompletionGenerator::OPTION_KEY_INTERFACE_NAME] = 'BundleAutoCompletion';

        $generator = new IdeBundleAutoCompletionGenerator($options);
        $generator
            ->addMethodTagBuilder(new ClientMethodTagBuilder());

        $generator->create();

        $this->info('Generated Client IdeBundleAutoCompletion file');
    }

    /**
     * @throws \Exception
     *
     * @return string
     */
    private function getProjectNamespace()
    {
        return Config::get(ApplicationConstants::PROJECT_NAMESPACES)[0];
    }

}
