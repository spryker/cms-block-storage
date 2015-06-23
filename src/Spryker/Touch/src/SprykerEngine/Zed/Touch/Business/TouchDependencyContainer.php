<?php
/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace SprykerEngine\Zed\Touch\Business;

use Generated\Zed\Ide\FactoryAutoCompletion\TouchBusiness;
use SprykerEngine\Zed\Kernel\Business\AbstractDependencyContainer;
use SprykerEngine\Zed\Touch\Business\Model\TouchRecordInterface;
use SprykerEngine\Zed\Touch\Persistence\TouchQueryContainerInterface;

/**
 * @method TouchBusiness getFactory()
 */
class TouchDependencyContainer extends AbstractDependencyContainer
{
    /**
     * @return TouchRecordInterface
     */
    public function getTouchRecordModel()
    {
        return $this->getFactory()->createModelTouchRecord(
            $this->getQueryContainer()
        );
    }

    /**
     * @return TouchQueryContainerInterface
     */
    protected function getQueryContainer()
    {
        return $this->getLocator()->touch()->queryContainer();
    }
}
