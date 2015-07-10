<?php

/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace SprykerFeature\Zed\Application\Business\Model\Navigation;

use SprykerFeature\Zed\Application\Business\Model\Navigation\Collector\NavigationCollectorInterface;
use SprykerFeature\Zed\Application\Business\Model\Navigation\Extractor\PathExtractorInterface;
use SprykerFeature\Zed\Application\Business\Model\Navigation\Formatter\MenuFormatterInterface;
use SprykerFeature\Zed\Application\Business\Model\Navigation\SchemaFinder\NavigationSchemaFinderInterface;

class NavigationBuilder
{

    const MENU = 'menu';
    const PATH = 'path';

    /**
     * @var NavigationSchemaFinderInterface
     */
    protected $navigationSchemaFinder;

    /**
     * @var NavigationCollectorInterface
     */
    protected $navigationCollector;

    /**
     * @var MenuFormatterInterface
     */
    protected $menuFormatter;

    /**
     * @param NavigationSchemaFinderInterface $navigationSchemaFinder
     * @param NavigationCollectorInterface $navigationCollector
     * @param MenuFormatterInterface $menuFormatter
     * @param PathExtractorInterface $pathExtractor
     */
    public function __construct(
        NavigationSchemaFinderInterface $navigationSchemaFinder,
        NavigationCollectorInterface $navigationCollector,
        MenuFormatterInterface $menuFormatter,
        PathExtractorInterface $pathExtractor
    ) {
        $this->navigationSchemaFinder = $navigationSchemaFinder;
        $this->navigationCollector = $navigationCollector;
        $this->menuFormatter = $menuFormatter;
        $this->pathExtractor = $pathExtractor;
    }

    /**
     * @param string $pathInfo
     *
     * @return array
     */
    public function build($pathInfo)
    {
        $navigationPages = $this->navigationCollector->mergeNavigationFiles(
            $this->navigationSchemaFinder
        );

        $menu = $this->menuFormatter->formatMenu($navigationPages, $pathInfo);
        $path = $this->pathExtractor->extractPathFromMenu($menu);

        return [
            self::MENU => $menu,
            self::PATH => $path,
        ];
    }

}
