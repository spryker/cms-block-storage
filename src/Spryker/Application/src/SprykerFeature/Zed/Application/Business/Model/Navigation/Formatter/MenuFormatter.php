<?php
/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace SprykerFeature\Zed\Application\Business\Model\Navigation\Formatter;

use SprykerFeature\Zed\Application\Business\Model\Navigation\Validator\MenuLevelValidatorInterface;
use SprykerFeature\Zed\Application\Business\Model\Navigation\Validator\UrlUniqueValidatorInterface;
use SprykerFeature\Zed\Application\Business\Model\Url\UrlBuilderInterface;

class MenuFormatter implements MenuFormatterInterface
{

    const VISIBLE = 'visible';
    const URI = 'uri';
    const ID = 'id';
    const ATTRIBUTES = 'attributes';
    const LABEL = 'label';
    const PAGES = 'pages';
    const CONTROLLER = 'controller';
    const INDEX = 'index';
    const ACTION = 'action';
    const BUNDLE = 'bundle';
    const CHILDREN = 'children';
    const TITLE = 'title';
    const SHORTCUT = 'shortcut';
    const IS_ACTIVE = 'is_active';
    const CHILD_IS_ACTIVE = 'child_is_active';

    /**
     * @var UrlUniqueValidatorInterface
     */
    protected $urlUniqueValidator;

    /**
     * @var MenuLevelValidatorInterface
     */
    protected $menuLevelValidator;

    /**
     * @var UrlBuilderInterface
     */
    protected $urlBuilder;

    /**
     * @param UrlUniqueValidatorInterface $urlUniqueValidator
     * @param MenuLevelValidatorInterface $menuLevelValidator
     * @param UrlBuilderInterface $urlBuilder
     */
    public function __construct(
        UrlUniqueValidatorInterface $urlUniqueValidator,
        MenuLevelValidatorInterface $menuLevelValidator,
        UrlBuilderInterface $urlBuilder
    ) {
        $this->urlUniqueValidator = $urlUniqueValidator;
        $this->menuLevelValidator = $menuLevelValidator;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * @param array $pages
     * @param string $pathInfo
     * @return array
     */
    public function formatMenu(array $pages, $pathInfo)
    {
        $formattedPages = $this->formatPages($pages, $pathInfo);
        unset($formattedPages[self::CHILD_IS_ACTIVE]);

        return $formattedPages;
    }

    /**
     * @param array $pages
     * @param string $pathInfo
     * @param int $currentLevel
     * @return array
     */
    protected function formatPages(array $pages, $pathInfo, $currentLevel = 1)
    {
        $formattedPages = [];
        $currentLevel++;
        foreach ($pages as $page) {
            if (isset($page[self::VISIBLE]) && !$page[self::VISIBLE]) {
                continue;
            }
            $formattedPage = $this->formatPage($page);
            if (isset($page[self::PAGES]) && !empty($page[self::PAGES])) {
                $this->menuLevelValidator->validate($currentLevel, $page[self::TITLE]);
                $children = $this->formatPages($page[self::PAGES], $pathInfo, $currentLevel);
            }
            if (isset($children[self::CHILD_IS_ACTIVE]) ||
                $pathInfo === $formattedPage[self::URI]
            ) {
                $formattedPages[self::CHILD_IS_ACTIVE] = true;
                $formattedPage[self::IS_ACTIVE] = true;
            }
            if (!empty($children)) {
                unset($children[self::CHILD_IS_ACTIVE]);
                $formattedPage[self::CHILDREN] = $children;
                $children = [];
            }
            $formattedPages[$formattedPage[self::TITLE]] = $formattedPage;
        }

        return $formattedPages;
    }

    /**
     * @param array $page
     * @return string
     */
    protected function getUri(array $page)
    {
        if (isset($page[self::URI]) && !empty($page[self::URI])) {
            return $page[self::URI];
        }
        $controller = (
            isset($page[self::CONTROLLER]) &&
            self::INDEX !== $page[self::CONTROLLER]) ? $page[self::CONTROLLER] : null;
        $action = (isset($page[self::ACTION]) && self::INDEX !== $page[self::ACTION]) ? $page[self::ACTION] : null;

        return $this->urlBuilder->build($page[self::BUNDLE], $controller, $action);
    }

    /**
     * @param array $page
     * @return array
     */
    protected function formatPage(array $page)
    {
        $formattedPage = [];

        $url = $this->getUri($page);
        if ('#' ==! $url) {
            $this->urlUniqueValidator->validate($url);
            $this->urlUniqueValidator->addUrl($url);
        }
        $formattedPage[self::URI] = $url;
        $formattedPage[self::LABEL] = $page[self::LABEL];
        $formattedPage[self::TITLE] = $page[self::TITLE];

        if (isset($page[self::SHORTCUT]) && strlen($page[self::SHORTCUT]) === 1) {
            $formattedPage[self::SHORTCUT] = $page[self::SHORTCUT];
        }

        $attributes = [];
        if (isset($page[self::ID])) {
            $attributes[self::ID] = $page[self::ID];
        }
        if (count($attributes)) {
            $formattedPage[self::ATTRIBUTES] = $attributes;
            return array($formattedPage, $page);
        }

        return $formattedPage;
    }
}
