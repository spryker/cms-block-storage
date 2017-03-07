<?php
/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Unit\Spryker\Zed\Development\Business\IdeAutoCompletion\Bundle;

use Codeception\TestCase\Test;
use Spryker\Zed\Development\Business\IdeAutoCompletion\Bundle\NamespaceExtractor;
use Symfony\Component\Finder\SplFileInfo;

/**
 * @group Unit
 * @group Spryker
 * @group Zed
 * @group Development
 * @group Business
 * @group IdeAutoCompletion
 * @group Bundle
 * @group NamespaceExtractorTest
 */
class NamespaceExtractorTest extends Test
{

    /**
     * @return void
     */
    public function testReplacementOfRegularBaseDirectory()
    {
        $baseDirectory = '/foo/bar/baz/Bundle/src/';
        $directory = new SplFileInfo('/foo/bar/baz/Bundle/src/Spryker/Application/Bundle', null, null);
        $namespaceExtractor = new NamespaceExtractor();
        $namespace = $namespaceExtractor->fromDirectory($directory, $baseDirectory);

        $this->assertSame('Spryker\Application\Bundle', $namespace);
    }

    /**
     * @return void
     */
    public function testReplacementOfAsteriskGlobPatternBaseDirectory()
    {
        $baseDirectory = '/foo/bar/baz/*/src/';
        $directory = new SplFileInfo('/foo/bar/baz/Bundle/src/Spryker/Application/Bundle', null, null);
        $namespaceExtractor = new NamespaceExtractor();
        $namespace = $namespaceExtractor->fromDirectory($directory, $baseDirectory);

        $this->assertSame('Spryker\Application\Bundle', $namespace);
    }

    /**
     * @return void
     */
    public function testReplacementOfQuestionMarkGlobPatternBaseDirectory()
    {
        $baseDirectory = '/foo/bar/?az/Bundle/src/';
        $directory = new SplFileInfo('/foo/bar/baz/Bundle/src/Spryker/Application/Bundle', null, null);
        $namespaceExtractor = new NamespaceExtractor();
        $namespace = $namespaceExtractor->fromDirectory($directory, $baseDirectory);

        $this->assertSame('Spryker\Application\Bundle', $namespace);
    }

    /**
     * @return void
     */
    public function testReplacementOfBraceGlobPatternBaseDirectory()
    {
        $baseDirectory = '/foo/bar/{baz,spryker}/Bundle/src/';
        $namespaceExtractor = new NamespaceExtractor();

        $directory = new SplFileInfo('/foo/bar/baz/Bundle/src/Spryker/Application/Bundle', null, null);
        $namespace = $namespaceExtractor->fromDirectory($directory, $baseDirectory);
        $this->assertSame('Spryker\Application\Bundle', $namespace);

        $directory = new SplFileInfo('/foo/bar/spryker/Bundle/src/Spryker/Application/Bundle', null, null);
        $namespace = $namespaceExtractor->fromDirectory($directory, $baseDirectory);
        $this->assertSame('Spryker\Application\Bundle', $namespace);
    }

    /**
     * @return void
     */
    public function testReplacementOfCharacterClassGlobPatternBasePath()
    {
        $baseDirectory = '/foo/bar/[bf]az/Bundle/src/';
        $namespaceExtractor = new NamespaceExtractor();

        $directory = new SplFileInfo('/foo/bar/baz/Bundle/src/Spryker/Application/Bundle', null, null);
        $namespace = $namespaceExtractor->fromDirectory($directory, $baseDirectory);
        $this->assertSame('Spryker\Application\Bundle', $namespace);

        $directory = new SplFileInfo('/foo/bar/faz/Bundle/src/Spryker/Application/Bundle', null, null);
        $namespace = $namespaceExtractor->fromDirectory($directory, $baseDirectory);
        $this->assertSame('Spryker\Application\Bundle', $namespace);
    }

}
