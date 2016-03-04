<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Unit\Spryker\Zed\Setup\Business\Model;

use Spryker\Zed\Setup\Business\Model\DirectoryRemover;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @group Spryker
 * @group Zed
 * @group Setup
 * @group Business
 * @group DirectoryRemover
 */
class DirectoryRemoverTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var string
     */
    protected $fixtureDirectory;

    /**
     * @return void
     */
    public function setUp()
    {
        $this->fixtureDirectory = __DIR__ . '/Fixtures';
        $directory = $this->fixtureDirectory . DIRECTORY_SEPARATOR . 'Foo';
        mkdir($directory, 0777, true);
        $filename = $directory . DIRECTORY_SEPARATOR . 'bar';
        touch($filename);

        $this->assertFileExists($filename);
    }

    /**
     * @return void
     */
    public function tearDown()
    {
        $filesystem = new Filesystem();
        $filesystem->remove($this->fixtureDirectory);
    }

    /**
     * @return void
     */
    public function testAfterExecutionGeneratedDirectoryMustBeRemoved()
    {
        $directoryRemover = new DirectoryRemover($this->fixtureDirectory);
        $directoryRemover->execute();

        $this->assertFalse(is_dir($this->fixtureDirectory));
    }

}
