<?php

namespace SprykerFeature\Sdk\CategoryExporter\Builder;

use SprykerFeature\Shared\FrontendExporter\Code\KeyBuilder\KeyBuilderInterface;
use SprykerFeature\Shared\KvStorage\Client\ReadInterface;

class CategoryTreeBuilder
{
    const SUBTREE_DEPTH = 3;

    /**
     * @var ReadInterface
     */
    protected $kvReader;

    /**
     * @var KeyBuilderInterface
     */
    protected $keyBuilder;

    /**
     * @param ReadInterface $kvReader
     * @param KeyBuilderInterface $keyBuilder
     */
    public function __construct(ReadInterface $kvReader, KeyBuilderInterface $keyBuilder)
    {
        $this->kvReader = $kvReader;
        $this->keyBuilder = $keyBuilder;
    }

    /**
     * @param array $categoryNode
     * @param string $locale
     *
     * @return array
     */
    public function createTreeFromCategoryNode(array $categoryNode, $locale)
    {
        $parents = array_slice(array_reverse($categoryNode['parents']), 0, self::SUBTREE_DEPTH);
        $subtree = [];
        foreach ($parents as $parent) {
            $storageKey = $this->keyBuilder->generateKey(
                $parent['node_id'],
                $locale
            );
            $parentCategory = $this->kvReader->get($storageKey);
            if (empty($subtree)) {
                $subtree = $parentCategory;
            }
            if ($parentCategory) {
                $parentCategory = $this->addCurrentSubtree($parentCategory, $subtree);
                $subtree = $parentCategory;
            }
        }

        if (empty($categoryNode['parents']) || empty($subtree)) {
            $subtree = $categoryNode;
        }

        $subtree['depth'] = self::SUBTREE_DEPTH;

        return $subtree;
    }

    /**
     * @param array $parentCategory
     * @param array $subtree
     *
     * @return array
     */
    protected function addCurrentSubtree(array $parentCategory, array $subtree)
    {
        foreach ($parentCategory['children'] as $key => $child) {
            if ($child['url'] == $subtree['url']) {
                $parentCategory['children'][$key]['children'] = $subtree['children'];
            }
        }

        return $parentCategory;
    }
}
