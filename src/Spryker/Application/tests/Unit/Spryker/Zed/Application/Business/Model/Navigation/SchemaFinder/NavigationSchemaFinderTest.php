<?php

/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace Unit\Spryker\Zed\Application\Business\Model\Navigation\SchemaFinder;

use Spryker\Zed\Application\Business\Model\Navigation\SchemaFinder\NavigationSchemaFinder;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @group Spryker
 * @group Zed
 * @group Application
 * @group Business
 * @group NavigationSchemaFinder
 */
class NavigationSchemaFinderTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @return void
     */
    public function setUp()
    {
        mkdir($this->getFixtureDirectory());
    }

    /**
     * @return void
     */
    public function tearDown()
    {
        $fileSystem = new Filesystem();
        $fileSystem->remove($this->getFixtureDirectory());
    }

    /**
     * @return string
     */
    private function getFixtureDirectory()
    {
        return __DIR__ . DIRECTORY_SEPARATOR . 'Fixtures';
    }

    /**
     * @return void
     */
    public function testGetSchemasShouldReturnIterateableFileCollection()
    {
        $schemaFinder = new NavigationSchemaFinder(
            [$this->getFixtureDirectory()],
            'file name pattern'
        );

        $this->assertInstanceOf('Symfony\Component\Finder\Finder', $schemaFinder->getSchemaFiles());
    }

}
