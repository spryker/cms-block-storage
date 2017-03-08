<?php

/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace PropelQueryBuilder\Module;

use Codeception\Module;
use Codeception\TestCase;
use Silex\Application;
use Spryker\Zed\Propel\Communication\Plugin\ServiceProvider\PropelServiceProvider;

class PropelQueryBuilder extends Module
{

    /**
     * @param array|null $config
     */
    public function __construct($config = null)
    {
        parent::__construct($config);

        $propelServiceProvider = new PropelServiceProvider();
        $propelServiceProvider->boot(new Application());
    }

    /**
     * @param \Codeception\TestCase $test
     *
     * @return void
     */
    public function _before(TestCase $test)
    {
        parent::_before($test);

        $this->cleanUpDatabase();
    }

    /**
     * @return void
     */
    public function _afterSuite()
    {
        parent::_afterSuite();

        $this->cleanUpDatabase();
    }

    /**
     * @param \Codeception\TestCase $test
     * @param bool $fail
     *
     * @return void
     */
    public function _failed(TestCase $test, $fail)
    {
        parent::_failed($test, $fail);

        $this->cleanUpDatabase();
    }

    /**
     * @return void
     */
    private function cleanUpDatabase()
    {
    }

}
