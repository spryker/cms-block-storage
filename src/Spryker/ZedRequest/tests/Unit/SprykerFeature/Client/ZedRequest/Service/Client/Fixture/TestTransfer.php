<?php
/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace Unit\SprykerFeature\Client\ZedRequest\Service\Client\Fixture;

use SprykerEngine\Shared\Transfer\AbstractTransfer;

class TestTransfer extends AbstractTransfer
{

    /**
     * @var string
     */
    protected $foo;

    /**
     * @return string
     */
    public function getFoo()
    {
        return $this->foo;
    }

    /**
     * @param string $foo
     * @return TestTransfer
     */
    public function setFoo($foo)
    {
        $this->foo = $foo;

        return $this;
    }


}
