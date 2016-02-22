<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Shared\Library\Storage\Adapter\Solr\Solarium\QueryType\Admin;

use Solarium\Core\Query\Query as BaseQuery;

/**
 * Class Query
 */
class Query extends BaseQuery
{

    const QUERY_ADMIN = 'admin';

    /**
     * Default options for the system query type.
     *
     * @var array
     */
    protected $options = [
        'resultclass' => 'Spryker\Shared\Library\Storage\Adapter\Solr\Solarium\QueryType\Admin\Result',
        'handler' => 'cores/',
    ];

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return self::QUERY_ADMIN;
    }

    /**
     * {@inheritdoc}
     */
    public function getRequestBuilder()
    {
        return new RequestBuilder();
    }

    /**
     * {@inheritdoc}
     */
    public function getResponseParser()
    {
        return new ResponseParser();
    }

}
