<?php

/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace Spryker\Zed\Setup\Business;

use Spryker\Zed\Setup\Business\Model\DirectoryRemover;
use Spryker\Zed\Kernel\Business\AbstractBusinessFactory;
use Spryker\Zed\Setup\Business\Model\Cronjobs;
use Spryker\Zed\Setup\Communication\Console\DeployPreparePropelConsole;
use Spryker\Zed\Setup\Communication\Console\GenerateClientIdeAutoCompletionConsole;
use Spryker\Zed\Setup\Communication\Console\GenerateIdeAutoCompletionConsole;
use Spryker\Zed\Setup\Communication\Console\GenerateZedIdeAutoCompletionConsole;
use Spryker\Zed\Setup\Communication\Console\InstallConsole;
use Spryker\Zed\Setup\Communication\Console\JenkinsDisableConsole;
use Spryker\Zed\Setup\Communication\Console\JenkinsEnableConsole;
use Spryker\Zed\Setup\Communication\Console\JenkinsGenerateConsole;
use Spryker\Zed\Setup\Communication\Console\Npm\RunnerConsole;
use Spryker\Zed\Setup\Communication\Console\RemoveGeneratedDirectoryConsole;
use Spryker\Zed\Setup\SetupDependencyProvider;

/**
 * @method \Spryker\Zed\Setup\SetupConfig getConfig()
 */
class SetupBusinessFactory extends AbstractBusinessFactory
{

    /**
     * @return \Spryker\Zed\Setup\Business\Model\Cronjobs
     */
    public function createModelCronjobs()
    {
        $config = $this->getConfig();

        return new Cronjobs($config);
    }

    /**
     * @return \Spryker\Zed\Setup\Business\Model\DirectoryRemoverInterface
     */
    public function createModelGeneratedDirectoryRemover()
    {
        return $this->createDirectoryRemover(
            $this->getConfig()->getGeneratedDirectory()
        );
    }

    /**
     * @param string $path
     *
     * @return \Spryker\Zed\Setup\Business\Model\DirectoryRemoverInterface
     */
    protected function createDirectoryRemover($path)
    {
        return new DirectoryRemover($path);
    }

    /**
     * @throws \ErrorException
     *
     * @return \Spryker\Zed\Application\Communication\Plugin\TransferObject\Repeater
     */
    public function getTransferObjectRepeater()
    {
        return $this->getProvidedDependency(SetupDependencyProvider::PLUGIN_TRANSFER_OBJECT_REPEATER);
    }

    /**
     * @deprecated Use getTransferObjectRepeater() instead.
     *
     * @return \Spryker\Zed\Application\Communication\Plugin\TransferObject\Repeater
     */
    public function createTransferObjectRepeater()
    {
        trigger_error('Deprecated, use getTransferObjectRepeater() instead.', E_USER_DEPRECATED);

        return $this->getTransferObjectRepeater();
    }

    /**
     * @return \Symfony\Component\Console\Command\Command[]
     */
    public function getConsoleCommands()
    {
        return [
            $this->createGenerateIdeAutoCompletionConsole(),
            $this->createGenerateZedIdeAutoCompletionConsole(),
            $this->createGenerateClientIdeAutoCompletionConsole(),
            $this->createRunnerConsole(),
            $this->createRemoveGeneratedDirectoryConsole(),
            $this->createInstallConsole(),
            $this->createJenkinsEnableConsole(),
            $this->createJenkinsDisableConsole(),
            $this->createJenkinsGenerateConsole(),
            $this->createDeployPreparePropelConsole(),
        ];
    }

    /**
     * @return \Spryker\Zed\Setup\Communication\Console\GenerateIdeAutoCompletionConsole
     */
    protected function createGenerateIdeAutoCompletionConsole()
    {
        return new GenerateIdeAutoCompletionConsole();
    }

    /**
     * @return \Spryker\Zed\Setup\Communication\Console\GenerateZedIdeAutoCompletionConsole
     */
    protected function createGenerateZedIdeAutoCompletionConsole()
    {
        return new GenerateZedIdeAutoCompletionConsole();
    }

    /**
     * @return \Spryker\Zed\Setup\Communication\Console\GenerateClientIdeAutoCompletionConsole
     */
    protected function createGenerateClientIdeAutoCompletionConsole()
    {
        return new GenerateClientIdeAutoCompletionConsole();
    }

    /**
     * @return \Spryker\Zed\Setup\Communication\Console\Npm\RunnerConsole
     */
    protected function createRunnerConsole()
    {
        return new RunnerConsole();
    }

    /**
     * @return \Spryker\Zed\Setup\Communication\Console\RemoveGeneratedDirectoryConsole
     */
    protected function createRemoveGeneratedDirectoryConsole()
    {
        return new RemoveGeneratedDirectoryConsole();
    }

    /**
     * @return \Spryker\Zed\Setup\Communication\Console\InstallConsole
     */
    protected function createInstallConsole()
    {
        return new InstallConsole();
    }

    /**
     * @return \Spryker\Zed\Setup\Communication\Console\JenkinsEnableConsole
     */
    protected function createJenkinsEnableConsole()
    {
        return new JenkinsEnableConsole();
    }

    /**
     * @return \Spryker\Zed\Setup\Communication\Console\JenkinsDisableConsole
     */
    protected function createJenkinsDisableConsole()
    {
        return new JenkinsDisableConsole();
    }

    /**
     * @return \Spryker\Zed\Setup\Communication\Console\JenkinsGenerateConsole
     */
    protected function createJenkinsGenerateConsole()
    {
        return new JenkinsGenerateConsole();
    }

    /**
     * @return \Spryker\Zed\Setup\Communication\Console\DeployPreparePropelConsole
     */
    protected function createDeployPreparePropelConsole()
    {
        return new DeployPreparePropelConsole();
    }

}
