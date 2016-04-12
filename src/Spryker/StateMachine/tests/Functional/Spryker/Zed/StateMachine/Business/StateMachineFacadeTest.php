<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Functional\Spryker\Zed\StateMachine\Business;

use Codeception\TestCase\Test;
use Functional\Spryker\Zed\StateMachine\Mocks\StateMachineConfig;
use Functional\Spryker\Zed\StateMachine\Mocks\TestStateMachineHandler;
use Generated\Shared\Transfer\StateMachineItemTransfer;
use Generated\Shared\Transfer\StateMachineProcessTransfer;
use Orm\Zed\StateMachine\Persistence\Base\SpyStateMachineEventTimeoutQuery;
use Orm\Zed\StateMachine\Persistence\Base\SpyStateMachineItemStateQuery;
use Orm\Zed\StateMachine\Persistence\Base\SpyStateMachineProcessQuery;
use Spryker\Zed\Graph\Communication\Plugin\GraphPlugin;
use Spryker\Zed\Kernel\Container;
use Spryker\Zed\StateMachine\Business\StateMachineBusinessFactory;
use Spryker\Zed\StateMachine\Business\StateMachineFacade;
use Spryker\Zed\StateMachine\Dependency\Plugin\StateMachineHandlerInterface;
use Spryker\Zed\StateMachine\StateMachineDependencyProvider;

class StateMachineFacadeTest extends Test
{

    /**
     * @return void
     */
    protected function setUp()
    {
        parent::setUp();
    }

    /**
     * @return void
     */
    public function testTriggerForNewStateMachineItemWhenInitialProcessIsSuccessShouldNotifyHandlerStateChange()
    {
        $processName = 'TestProcess';
        $identifier = 1985;
        $stateMachineName = 'Test';

        $stateMachineProcessTransfer = new StateMachineProcessTransfer();
        $stateMachineProcessTransfer->setProcessName($processName);
        $stateMachineProcessTransfer->setStateMachineName($stateMachineName);

        $stateMachineHandler = new TestStateMachineHandler();
        $stateMachineFacade = $this->createStateMachineFacade($stateMachineHandler);

        $triggerResult = $stateMachineFacade->triggerForNewStateMachineItem($stateMachineProcessTransfer, $identifier);

        $stateMachineItemTransfer = $stateMachineHandler->getItemStateUpdated();

        $stateMachineProcessEntity = SpyStateMachineProcessQuery::create()
            ->filterByName($processName)
            ->filterByStateMachineName($stateMachineName)
            ->findOne();

        $stateMachineItemStateEntity = SpyStateMachineItemStateQuery::create()
            ->joinProcess()
            ->filterByFkStateMachineProcess($stateMachineProcessEntity->getIdStateMachineProcess())
            ->filterByName($stateMachineHandler->getInitialStateForProcess($processName))
            ->findOne();

        $this->assertNotEmpty($stateMachineItemStateEntity);
        $this->assertTrue($triggerResult);
        $this->assertEquals($identifier, $stateMachineItemTransfer->getIdentifier());
        $this->assertEquals('order exported', $stateMachineItemTransfer->getStateName());
        $this->assertEquals($processName, $stateMachineItemTransfer->getProcessName());
    }

    /**
     * @return void
     */
    public function testTriggerEventForItemWithManualEventShouldMoveToNextStateWithManualEvent()
    {
        $processName = 'TestProcess';
        $identifier = 1985;
        $stateMachineName = 'Test';

        $stateMachineProcessTransfer = new StateMachineProcessTransfer();
        $stateMachineProcessTransfer->setProcessName($processName);
        $stateMachineProcessTransfer->setStateMachineName($stateMachineName);

        $stateMachineHandler = new TestStateMachineHandler();
        $stateMachineFacade = $this->createStateMachineFacade($stateMachineHandler);

        $stateMachineFacade->triggerForNewStateMachineItem($stateMachineProcessTransfer, $identifier);

        $stateMachineItemTransfer = $stateMachineHandler->getItemStateUpdated();

        $triggerResult = $stateMachineFacade->triggerEvent(
            'ship order',
            $stateMachineName,
            $stateMachineItemTransfer
        );

        $stateMachineItemTransfer = $stateMachineHandler->getItemStateUpdated();

        $this->assertTrue($triggerResult);
        $this->assertEquals('waiting for payment', $stateMachineItemTransfer->getStateName());
        $this->assertEquals($processName, $stateMachineItemTransfer->getProcessName());
        $this->assertEquals($identifier, $stateMachineItemTransfer->getIdentifier());
    }

    /**
     * @return void
     */
    public function testGetProcessesShouldReturnListOfProcessesAddedToHandler()
    {
        $processName = 'TestProcess';
        $stateMachineName = 'Test';

        $stateMachineProcessTransfer = new StateMachineProcessTransfer();
        $stateMachineProcessTransfer->setProcessName($processName);
        $stateMachineProcessTransfer->setStateMachineName($stateMachineName);

        $stateMachineHandler = new TestStateMachineHandler();
        $stateMachineFacade = $this->createStateMachineFacade($stateMachineHandler);

        $processList = $stateMachineFacade->getProcesses($stateMachineName);

        $this->assertCount(1, $processList);

        /* @var $process ProcessInterface  */
        $process = array_pop($processList);
        $this->assertEquals($processName, $process->getName());

    }

    /**
     * @return void
     */
    public function testGetStateMachineProcessIdShouldReturnIdStoredInPersistence()
    {
        $processName = 'TestProcess';
        $stateMachineName = 'Test';

        $stateMachineHandler = new TestStateMachineHandler();
        $stateMachineFacade = $this->createStateMachineFacade($stateMachineHandler);

        $stateMachineProcessTransfer = new StateMachineProcessTransfer();
        $stateMachineProcessTransfer->setProcessName($processName);
        $stateMachineProcessTransfer->setStateMachineName($stateMachineName);

        $processId = $stateMachineFacade->getStateMachineProcessId($stateMachineProcessTransfer);

        $stateMachineProcessEntity = SpyStateMachineProcessQuery::create()
            ->filterByName($processName)
            ->filterByStateMachineName($stateMachineName)
            ->findOne();

        $this->assertEquals($stateMachineProcessEntity->getIdStateMachineProcess(), $processId);
    }

    /**
     * @return void
     */
    public function testGetManualEventsForStateMachineItemShouldReturnsAllManualEventsForProvidedState()
    {
        $processName = 'TestProcess';
        $identifier = 1985;
        $stateMachineName = 'Test';

        $stateMachineProcessTransfer = new StateMachineProcessTransfer();
        $stateMachineProcessTransfer->setProcessName($processName);
        $stateMachineProcessTransfer->setStateMachineName($stateMachineName);

        $stateMachineHandler = new TestStateMachineHandler();
        $stateMachineFacade = $this->createStateMachineFacade($stateMachineHandler);

        $stateMachineFacade->triggerForNewStateMachineItem($stateMachineProcessTransfer, $identifier);

        $stateMachineItemTransfer = $stateMachineHandler->getItemStateUpdated();

        $manualEvents = $stateMachineFacade->getManualEventsForStateMachineItem(
            $stateMachineName,
            $stateMachineItemTransfer
        );

        $this->assertEquals('order exported', $stateMachineItemTransfer->getStateName());
        $this->assertCount(2, $manualEvents);

        /* @var $manualEvent EventInterface  */
        $manualEvent = array_pop($manualEvents);
        $this->assertEquals('check with condition', $manualEvent);

        /* @var $manualEvent EventInterface  */
        $manualEvent = array_pop($manualEvents);
        $this->assertEquals('ship order', $manualEvent);

    }

    /**
     * @return void
     */
    public function testGetManualEventForStateMachineItemsShouldReturnAllEventsForProvidedStates()
    {
        $processName = 'TestProcess';
        $stateMachineName = 'Test';
        $firstItemIdentifier = 1985;
        $secondItemIdentifier = 1988;

        $stateMachineProcessTransfer = new StateMachineProcessTransfer();
        $stateMachineProcessTransfer->setProcessName($processName);
        $stateMachineProcessTransfer->setStateMachineName($stateMachineName);

        $stateMachineHandler = new TestStateMachineHandler();
        $stateMachineFacade = $this->createStateMachineFacade($stateMachineHandler);

        $stateMachineFacade->triggerForNewStateMachineItem($stateMachineProcessTransfer, $firstItemIdentifier);
        $stateMachineItems[$firstItemIdentifier] = $stateMachineHandler->getItemStateUpdated();

        $stateMachineFacade->triggerForNewStateMachineItem($stateMachineProcessTransfer, $secondItemIdentifier);
        $stateMachineItems[$secondItemIdentifier] = $stateMachineHandler->getItemStateUpdated();

        $stateMachineFacade->triggerEvent('ship order', $stateMachineName, $stateMachineItems[$secondItemIdentifier]);

        $manualEvents = $stateMachineFacade->getManualEventsForStateMachineItems($stateMachineName, $stateMachineItems);

        $this->assertCount(2, $manualEvents);

        $firstItemManualEvents = $manualEvents[$firstItemIdentifier];
        $secondItemManualEvents = $manualEvents[$secondItemIdentifier];

        /* @var $manualEvent EventInterface  */
        $manualEvent = array_pop($firstItemManualEvents);
        $this->assertEquals('check with condition', $manualEvent);

        /* @var $manualEvent EventInterface  */
        $manualEvent = array_pop($firstItemManualEvents);
        $this->assertEquals('ship order', $manualEvent);

        /* @var $manualEvent EventInterface  */
        $manualEvent = array_pop($secondItemManualEvents);
        $this->assertEquals('payment received', $manualEvent);
    }

    /**
     * @return void
     */
    public function testGetProcessedStateMachineItemsShouldReturnItemsByProvidedStateIdsStoredInPersistence()
    {
        $processName = 'TestProcess';
        $stateMachineName = 'Test';
        $firstItemIdentifier = 1985;
        $secondItemIdentifier = 1988;

        $stateMachineProcessTransfer = new StateMachineProcessTransfer();
        $stateMachineProcessTransfer->setProcessName($processName);
        $stateMachineProcessTransfer->setStateMachineName($stateMachineName);

        $stateMachineHandler = new TestStateMachineHandler();
        $stateMachineFacade = $this->createStateMachineFacade($stateMachineHandler);

        $stateMachineFacade->triggerForNewStateMachineItem($stateMachineProcessTransfer, $firstItemIdentifier);
        $stateMachineItems[] = $stateMachineHandler->getItemStateUpdated();

        $stateMachineFacade->triggerForNewStateMachineItem($stateMachineProcessTransfer, $secondItemIdentifier);
        $stateMachineItems[] = $stateMachineHandler->getItemStateUpdated();

        $stateMachineHandler = new TestStateMachineHandler();
        $stateMachineFacade = $this->createStateMachineFacade($stateMachineHandler);

        /* @var $updatedStateMachineItems StateMachineItemTransfer[]  */
        $updatedStateMachineItems = $stateMachineFacade->getProcessedStateMachineItems(
            $stateMachineName,
            $stateMachineItems
        );

        $this->assertCount(2, $updatedStateMachineItems);

        $firstUpdatedStateMachineItemTransfer = $updatedStateMachineItems[0];
        $firstBeforeUpdateStateMachineItemTransfer = $stateMachineItems[0];
        $this->assertEquals(
            $firstUpdatedStateMachineItemTransfer->getIdItemState(),
            $firstBeforeUpdateStateMachineItemTransfer->getIdItemState()
        );
        $this->assertEquals(
            $firstUpdatedStateMachineItemTransfer->getProcessName(),
            $firstBeforeUpdateStateMachineItemTransfer->getProcessName()
        );
        $this->assertEquals(
            $firstUpdatedStateMachineItemTransfer->getIdStateMachineProcess(),
            $firstBeforeUpdateStateMachineItemTransfer->getIdStateMachineProcess()
        );
        $this->assertEquals(
            $firstUpdatedStateMachineItemTransfer->getStateName(),
            $firstBeforeUpdateStateMachineItemTransfer->getStateName()
        );
        $this->assertEquals(
            $firstUpdatedStateMachineItemTransfer->getIdentifier(),
            $firstBeforeUpdateStateMachineItemTransfer->getIdentifier()
        );

        $secondUpdatedStateMachineItemTransfer = $updatedStateMachineItems[1];
        $secondBeforeUpdateStateMachineItemTransfer = $stateMachineItems[1];
        $this->assertEquals(
            $secondUpdatedStateMachineItemTransfer->getIdItemState(),
            $secondBeforeUpdateStateMachineItemTransfer->getIdItemState()
        );
        $this->assertEquals(
            $secondUpdatedStateMachineItemTransfer->getProcessName(),
            $secondBeforeUpdateStateMachineItemTransfer->getProcessName()
        );
        $this->assertEquals(
            $secondUpdatedStateMachineItemTransfer->getIdStateMachineProcess(),
            $secondBeforeUpdateStateMachineItemTransfer->getIdStateMachineProcess()
        );
        $this->assertEquals(
            $secondUpdatedStateMachineItemTransfer->getStateName(),
            $secondBeforeUpdateStateMachineItemTransfer->getStateName()
        );
        $this->assertEquals(
            $secondUpdatedStateMachineItemTransfer->getIdentifier(),
            $secondBeforeUpdateStateMachineItemTransfer->getIdentifier()
        );
    }

    /**
     * @return void
     */
    public function testGetProcessedStateMachineItemTransferShouldReturnItemTransfer()
    {
        $processName = 'TestProcess';
        $stateMachineName = 'Test';
        $firstItemIdentifier = 1985;

        $stateMachineProcessTransfer = new StateMachineProcessTransfer();
        $stateMachineProcessTransfer->setProcessName($processName);
        $stateMachineProcessTransfer->setStateMachineName($stateMachineName);

        $stateMachineHandler = new TestStateMachineHandler();
        $stateMachineFacade = $this->createStateMachineFacade($stateMachineHandler);

        $stateMachineFacade->triggerForNewStateMachineItem($stateMachineProcessTransfer, $firstItemIdentifier);
        $stateMachineItemTransfer = $stateMachineHandler->getItemStateUpdated();

        $stateMachineHandler = new TestStateMachineHandler();
        $stateMachineFacade = $this->createStateMachineFacade($stateMachineHandler);

        $updatedStateMachineItemTransfer = $stateMachineFacade->getProcessedStateMachineItemTransfer(
            $stateMachineName,
            $stateMachineItemTransfer->getIdItemState(),
            $stateMachineItemTransfer->getIdStateMachineProcess(),
            $stateMachineItemTransfer->getIdentifier()
        );

        $this->assertEquals(
            $updatedStateMachineItemTransfer->getIdItemState(),
            $stateMachineItemTransfer->getIdItemState()
        );
        $this->assertEquals(
            $updatedStateMachineItemTransfer->getProcessName(),
            $stateMachineItemTransfer->getProcessName()
        );
        $this->assertEquals(
            $updatedStateMachineItemTransfer->getIdStateMachineProcess(),
            $stateMachineItemTransfer->getIdStateMachineProcess()
        );
        $this->assertEquals(
            $updatedStateMachineItemTransfer->getStateName(),
            $stateMachineItemTransfer->getStateName()
        );
        $this->assertEquals(
            $updatedStateMachineItemTransfer->getIdentifier(),
            $stateMachineItemTransfer->getIdentifier()
        );
    }

    /**
     * @return void
     */
    public function testGetStateHistoryByStateItemIdentifierShouldReturnAllHistoryForThatItem()
    {
        $processName = 'TestProcess';
        $stateMachineName = 'Test';
        $identifier = 1985;

        $stateMachineProcessTransfer = new StateMachineProcessTransfer();
        $stateMachineProcessTransfer->setProcessName($processName);
        $stateMachineProcessTransfer->setStateMachineName($stateMachineName);

        $stateMachineHandler = new TestStateMachineHandler();
        $stateMachineFacade = $this->createStateMachineFacade($stateMachineHandler);

        $stateMachineFacade->triggerForNewStateMachineItem($stateMachineProcessTransfer, $identifier);
        $stateMachineItemTransfer = $stateMachineHandler->getItemStateUpdated();

        $stateMachineItemsTransfer = $stateMachineFacade->getStateHistoryByStateItemIdentifier(
            $stateMachineName,
            $stateMachineItemTransfer->getIdStateMachineProcess(),
            $identifier
        );

        $this->assertCount(3, $stateMachineItemsTransfer);

        $stateMachineItemTransfer = $stateMachineItemsTransfer[0];
        $this->assertEquals('invoice created', $stateMachineItemTransfer->getStateName());

        $stateMachineItemTransfer = $stateMachineItemsTransfer[1];
        $this->assertEquals('invoice sent', $stateMachineItemTransfer->getStateName());

        $stateMachineItemTransfer = $stateMachineItemsTransfer[2];
        $this->assertEquals('order exported', $stateMachineItemTransfer->getStateName());

    }

    /**
     * @return void
     */
    public function testGetItemsWithFlagShouldReturnListOfStateMachineItemsWithGivenFlag()
    {
        $processName = 'TestProcess';
        $stateMachineName = 'Test';
        $identifier = 1985;

        $stateMachineProcessTransfer = new StateMachineProcessTransfer();
        $stateMachineProcessTransfer->setProcessName($processName);
        $stateMachineProcessTransfer->setStateMachineName($stateMachineName);

        $stateMachineHandler = new TestStateMachineHandler();
        $stateMachineFacade = $this->createStateMachineFacade($stateMachineHandler);

        $stateMachineFacade->triggerForNewStateMachineItem($stateMachineProcessTransfer, $identifier);

        $stateMachineItemsWithGivenFlag = $stateMachineFacade->getItemsWithFlag(
            $stateMachineProcessTransfer,
            'Flag1'
        );

        $this->assertCount(2, $stateMachineItemsWithGivenFlag);

        $stateMachineItemTransfer = $stateMachineItemsWithGivenFlag[0];
        $this->assertInstanceOf(StateMachineItemTransfer::class, $stateMachineItemTransfer);
        $this->assertEquals('invoice created', $stateMachineItemTransfer->getStateName());
        $this->assertEquals($identifier, $stateMachineItemTransfer->getIdentifier());

        $stateMachineItemTransfer = $stateMachineItemsWithGivenFlag[1];
        $this->assertEquals('invoice sent', $stateMachineItemTransfer->getStateName());
        $this->assertEquals($identifier, $stateMachineItemTransfer->getIdentifier());

        $stateMachineItemsWithGivenFlag = $stateMachineFacade->getItemsWithFlag(
            $stateMachineProcessTransfer,
            'Flag2'
        );

        $this->assertCount(1, $stateMachineItemsWithGivenFlag);
    }

    /**
     * @return void
     */
    public function testGetItemsWithoutFlagShouldReturnListOfStateMachineItemsWithoutGivenFlag()
    {
        $processName = 'TestProcess';
        $stateMachineName = 'Test';
        $identifier = 1985;

        $stateMachineProcessTransfer = new StateMachineProcessTransfer();
        $stateMachineProcessTransfer->setProcessName($processName);
        $stateMachineProcessTransfer->setStateMachineName($stateMachineName);

        $stateMachineHandler = new TestStateMachineHandler();
        $stateMachineFacade = $this->createStateMachineFacade($stateMachineHandler);

        $stateMachineFacade->triggerForNewStateMachineItem($stateMachineProcessTransfer, $identifier);

        $stateMachineItemsWithoutGivenFlag = $stateMachineFacade->getItemsWithoutFlag(
            $stateMachineProcessTransfer,
            'Flag1'
        );

        $this->assertCount(1, $stateMachineItemsWithoutGivenFlag);

        $stateMachineItemTransfer = $stateMachineItemsWithoutGivenFlag[0];
        $this->assertInstanceOf(StateMachineItemTransfer::class, $stateMachineItemTransfer);
        $this->assertEquals('order exported', $stateMachineItemTransfer->getStateName());
        $this->assertEquals($identifier, $stateMachineItemTransfer->getIdentifier());

        $stateMachineItemsWithoutGivenFlag = $stateMachineFacade->getItemsWithoutFlag(
            $stateMachineProcessTransfer,
            'Flag2'
        );

        $stateMachineItemTransfer = $stateMachineItemsWithoutGivenFlag[0];
        $this->assertEquals('invoice created', $stateMachineItemTransfer->getStateName());
        $this->assertEquals($identifier, $stateMachineItemTransfer->getIdentifier());

        $stateMachineItemTransfer = $stateMachineItemsWithoutGivenFlag[1];
        $this->assertEquals('order exported', $stateMachineItemTransfer->getStateName());
        $this->assertEquals($identifier, $stateMachineItemTransfer->getIdentifier());

        $this->assertCount(2, $stateMachineItemsWithoutGivenFlag);
    }

    /**
     * @return void
     */
    public function testCheckConditionsShouldProcessStatesWithConditionAndWithoutEvent()
    {
        $processName = 'TestProcess';
        $stateMachineName = 'Test';
        $identifier = 1985;

        $stateMachineProcessTransfer = new StateMachineProcessTransfer();
        $stateMachineProcessTransfer->setProcessName($processName);
        $stateMachineProcessTransfer->setStateMachineName($stateMachineName);

        $stateMachineHandler = new TestStateMachineHandler();
        $stateMachineFacade = $this->createStateMachineFacade($stateMachineHandler);

        $stateMachineFacade->triggerForNewStateMachineItem($stateMachineProcessTransfer, $identifier);

        $stateMachineItemTransfer = $stateMachineHandler->getItemStateUpdated();

        $stateMachineFacade->triggerEvent('check with condition', $stateMachineName, $stateMachineItemTransfer);

        $stateMachineHandler->setStateMachineItemsByStateIds([$stateMachineItemTransfer]);

        $stateMachineFacade->checkConditions($stateMachineName);

        $stateMachineItemTransfer = $stateMachineHandler->getItemStateUpdated();

        $this->assertEquals('waiting for payment', $stateMachineItemTransfer->getStateName());
    }

    /**
     * @return void
     */
    public function testCheckTimeoutsShouldMoveStatesWithExpiredTimeouts()
    {
        $processName = 'TestProcess';
        $stateMachineName = 'Test';
        $identifier = 1985;

        $stateMachineProcessTransfer = new StateMachineProcessTransfer();
        $stateMachineProcessTransfer->setProcessName($processName);
        $stateMachineProcessTransfer->setStateMachineName($stateMachineName);

        $stateMachineHandler = new TestStateMachineHandler();
        $stateMachineFacade = $this->createStateMachineFacade($stateMachineHandler);

        $stateMachineFacade->triggerForNewStateMachineItem($stateMachineProcessTransfer, $identifier);

        $stateMachineItemTransfer = $stateMachineHandler->getItemStateUpdated();

        $stateMachineFacade->triggerEvent('ship order', $stateMachineName, $stateMachineItemTransfer);

        $stateMachineItemTransfer = $stateMachineHandler->getItemStateUpdated();

        $stateMachineItemEventTimeoutEntity = SpyStateMachineEventTimeoutQuery::create()
            ->filterByIdentifier($stateMachineItemTransfer->getIdentifier())
            ->filterByFkStateMachineProcess($stateMachineItemTransfer->getIdStateMachineProcess())
            ->findOne();

        $stateMachineItemEventTimeoutEntity->setTimeout('1985-07-01');
        $stateMachineItemEventTimeoutEntity->save();

        $affectedItems = $stateMachineFacade->checkTimeouts($stateMachineName);

        $stateMachineItemTransfer = $stateMachineHandler->getItemStateUpdated();

        $this->assertEquals(1, $affectedItems);
        $this->assertEquals('reminder I sent', $stateMachineItemTransfer->getStateName());
    }

    /**
     * @param \Spryker\Zed\StateMachine\Dependency\Plugin\StateMachineHandlerInterface $stateMachineHandler
     *
     * @return \Spryker\Zed\StateMachine\Business\StateMachineFacade
     */
    protected function createStateMachineFacade(StateMachineHandlerInterface $stateMachineHandler)
    {
        $stateMachineBusinessFactory = new StateMachineBusinessFactory();
        $stateMachineConfig = new StateMachineConfig();
        $stateMachineBusinessFactory->setConfig($stateMachineConfig);

        $container = new Container();
        $container[StateMachineDependencyProvider::PLUGINS_STATE_MACHINE_HANDLERS] = function () use ($stateMachineHandler) {
            return [
               $stateMachineHandler,
            ];
        };

        $container[StateMachineDependencyProvider::PLUGIN_GRAPH] = function () {
             return new GraphPlugin();
        };

        $stateMachineBusinessFactory->setContainer($container);

        $stateMachineFacade = new StateMachineFacade();
        $stateMachineFacade->setFactory($stateMachineBusinessFactory);

        return $stateMachineFacade;
    }

}
