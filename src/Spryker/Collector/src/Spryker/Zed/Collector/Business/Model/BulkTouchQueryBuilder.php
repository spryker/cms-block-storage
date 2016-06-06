<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Collector\Business\Model;

use Spryker\Zed\Collector\CollectorConfig;

class BulkTouchQueryBuilder
{

    /**
     * @var \Spryker\Zed\Collector\CollectorConfig
     */
    protected $config;

    /**
     * @param \Spryker\Zed\Collector\CollectorConfig $config
     */
    public function __construct(CollectorConfig $config)
    {
        $this->config = $config;
    }

    /**
     * @throws \InvalidArgumentException
     *
     * @return \Spryker\Zed\Collector\Persistence\Pdo\BulkUpdateTouchByIdQueryInterface
     */
    public function createBulkTouchUpdateQuery()
    {
        $className = CollectorConfig::COLLECTOR_BULK_UPDATE_QUERY_CLASS;
        $resolvedClassName = $this->getResolvedClassName($className);

        if (!$resolvedClassName) {
            throw new \InvalidArgumentException('Can\'t resolve bulk touch class name: ' . $className);
        }

        return new $resolvedClassName();
    }

    /**
     * @throws \InvalidArgumentException
     *
     * @return \Spryker\Zed\Collector\Persistence\Pdo\BulkDeleteTouchByIdQueryInterface
     */
    public function createBulkTouchDeleteQuery()
    {
        $className = CollectorConfig::COLLECTOR_BULK_DELETE_QUERY_CLASS;
        $resolvedClassName = $this->getResolvedClassName($className);

        if (!$resolvedClassName) {
            throw new \InvalidArgumentException('Can\'t resolve bulk touch class name: ' . $className);
        }

        return new $resolvedClassName();
    }

    /**
     * @param string $className
     *
     * @return string|false
     */
    protected function getResolvedClassName($className)
    {
        $classList = $this->config->getCurrentBulkQueryClassNames();

        if (!isset($classList[$className])) {
            return false;
        }

        return $classList[$className];
    }

}
