<?php

/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace Spryker\Zed\Application\Business\Model\Navigation;

use Spryker\Zed\Application\Business\Model\Navigation\Collector\NavigationCollectorInterface;
use Spryker\Zed\Application\Business\Model\Navigation\Extractor\PathExtractorInterface;
use Spryker\Zed\Application\Business\Model\Navigation\Formatter\MenuFormatterInterface;

class NavigationBuilder
{

    const MENU = 'menu';
    const PATH = 'path';

    /**
     * @var NavigationCollectorInterface
     */
    private $navigationCollector;

    /**
     * @var MenuFormatterInterface
     */
    private $menuFormatter;

    /**
     * @param NavigationCollectorInterface $navigationCollector
     * @param MenuFormatterInterface $menuFormatter
     * @param PathExtractorInterface $pathExtractor
     */
    public function __construct(
        NavigationCollectorInterface $navigationCollector,
        MenuFormatterInterface $menuFormatter,
        PathExtractorInterface $pathExtractor
    ) {
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
        $navigationPages = $this->navigationCollector->getNavigation();

        $menu = $this->menuFormatter->formatMenu($navigationPages, $pathInfo);
        $breadcrumb = $this->menuFormatter->formatMenu($navigationPages, $pathInfo, true);
        $path = $this->pathExtractor->extractPathFromMenu($breadcrumb);

        return [
            self::MENU => $menu,
            self::PATH => $path,
        ];
    }

}
