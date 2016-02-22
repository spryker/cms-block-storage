<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Setup\Communication\Controller;

use Spryker\Shared\Config;
use Spryker\Shared\Library\Environment;
use Spryker\Shared\Setup\SetupConstants;
use Spryker\Shared\Application\ApplicationConstants;
use Spryker\Zed\Application\Communication\Controller\AbstractController;

class IndexController extends AbstractController
{

    public function indexAction()
    {
        $developmentLinks = [];

        if (APPLICATION_ENV !== 'production') {
            $developmentLinks[] = [
                'href' => '/setup/transfer/repeat',
                'target' => '_blank',
                'label' => __('Repeat last Yves-request'),
            ];
            $developmentLinks[] = [
                'href' => '/glossary/dump',
                'target' => '_blank',
                'label' => __('Dump glossary data to file'),
            ];
        }
        $developmentLinks[] = [
            'href' => '/setup/phpinfo',
            'target' => '_blank',
            'label' => __('Show PHP-Info'),
        ];
        if (Environment::isNotDevelopment()) {
            $developmentLinks[] = [
                'href' => '#',
                'label' => __('Show Elasticsearch' . ' <span class="icon-info"></span>'),
                'extras' => [
                    'data-toggle' => 'modal',
                    'data-target' => '#elastic',
                ],
            ];
        } else {
            $developmentLinks[] = [
                'href' => 'http://' . Config::get(ApplicationConstants::HOST_ZED_GUI) . ':9200',
                'target' => '_blank',
                'label' => __('Show Elasticsearch'),
            ];
        }
        if (Environment::isNotDevelopment()) {
            $developmentLinks[] = [
                'href' => '#',
                'label' => __('Show Elasticsearch Head (9200/_plugin/head)' . ' <span class="icon-info"></span>'),
                'extras' => [
                    'data-toggle' => 'modal',
                    'data-target' => '#elasticHead',
                ],
            ];
        } else {
            $developmentLinks[] = [
                'href' => 'http://' . Config::get(ApplicationConstants::HOST_ZED_GUI) . ':9200/_plugin/head',
                'target' => '_blank',
                'label' => __('Show Elasticsearch Head'),
            ];
        }

        if (Environment::isNotDevelopment()) {
            $developmentLinks[] = [
                'href' => '#',
                'label' => __('Show Elasticsearch Bigdesk (9200/_plugin/bigdesk)' . ' <span class="icon-info"></span>'),
                'extras' => [
                    'data-toggle' => 'modal',
                    'data-target' => '#elasticBigdeskModal',
                ],
            ];
        } else {
            $developmentLinks[] = [
                'href' => 'http://' . Config::get(ApplicationConstants::HOST_ZED_GUI) . ':9200/_plugin/bigdesk',
                'target' => '_blank',
                'label' => __('Show Elasticsearch Bigdesk'),
            ];
        }
        if (Environment::isNotDevelopment()) {
            $developmentLinks[] = [
                'href' => '#',
                'label' => __('Show Couchbase' . ' <span class="icon-info"></span>'),
                'extras' => [
                    'data-toggle' => 'modal',
                    'data-target' => '#couchbaseModal',
                ],
            ];
        } else {
            $developmentLinks[] = [
                'href' => 'http://' . Config::get(ApplicationConstants::HOST_ZED_GUI) . ':8091',
                'target' => '_blank',
                'label' => __('Show Couchbase'),
            ];
        }

        if (Environment::isNotDevelopment()) {
            $developmentLinks[] = [
                'href' => '#',
                'label' => __('Show Jenkins' . ' <span class="icon-info"></span>'),
                'extras' => [
                    'data-toggle' => 'modal',
                    'data-target' => '#jenkinsModal',
                ],
            ];
        } else {
            $developmentLinks[] = [
                'href' => Config::get(SetupConstants::JENKINS_BASE_URL),
                'target' => '_blank',
                'label' => __('Jenkins'),
            ];
        }

        $developmentLinks[] = [
            'href' => 'URL IS MISSING',
            'target' => '_blank',
            'label' => __('Install / Update Cronjobs'),
        ];

        return $this->viewResponse([
            'developmentLinks' => $developmentLinks,
        ]);
    }

    public function showCronjobsAction()
    {
        return $this->viewResponse([
            'jobs' => $this->facadeSetup->getAllCronjobs(),
        ]);
    }

    protected function getClient()
    {
        $redis = Redis::getInstance();

        return $redis->connect();
    }

    /**
     * @return void
     */
    public function redisAddAction()
    {
        $redis = $this->getClient();

        for ($i = 0; $i < 100; $i++) {
            $redis->set(md5($i), microtime(true));
        }

        echo '<pre>';
        var_dump($i);
        echo '<hr>';
        echo __FILE__ . ' ' . __LINE__;
        die;
    }

}
