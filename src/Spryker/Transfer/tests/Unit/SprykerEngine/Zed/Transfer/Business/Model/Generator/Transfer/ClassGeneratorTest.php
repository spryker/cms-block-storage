<?php

/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace Unit\Spryker\Zed\Transfer\Business\Model\Generator\TransferInterface;

use Spryker\Zed\Transfer\Business\Model\Generator\Transfer\ClassDefinition;
use Spryker\Zed\Transfer\Business\Model\Generator\Transfer\ClassGenerator;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @group Spryker
 * @group Zed
 * @group Transfer
 * @group Business
 * @group ClassGenerator
 */
class ClassGeneratorTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @return void
     */
    public function setUp()
    {
        $this->removeTargetDirectory();
    }

    /**
     * @return void
     */
    public function tearDown()
    {
        $this->removeTargetDirectory();
    }

    /**
     * @return void
     */
    private function removeTargetDirectory()
    {
        $filesystem = new Filesystem();
        $filesystem->remove($this->getFixtureDirectory());
    }

    /**
     * @return string
     */
    private function getFixtureDirectory()
    {
        return __DIR__ . '/Fixtures/';
    }

    /**
     * @return void
     */
    public function testGenerateShouldCreateTargetDirectoryIfNotExist()
    {
        $transferGenerator = new ClassGenerator($this->getFixtureDirectory());
        $transferDefinition = new ClassDefinition();
        $transferDefinition->setDefinition([
            'name' => 'Name',
        ]);
        $transferGenerator->generate($transferDefinition);

        $this->assertTrue(is_dir($this->getFixtureDirectory()));
    }

}
