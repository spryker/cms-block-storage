<?php
/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\StateMachine\Business\StateMachine;

use Generated\Shared\Transfer\StateMachineItemTransfer;
use Spryker\Zed\StateMachine\Business\Exception\StateMachineException;
use Spryker\Zed\StateMachine\Business\Process\ProcessInterface;
use Spryker\Zed\StateMachine\Persistence\StateMachineQueryContainerInterface;

class StateUpdater implements StateUpdaterInterface
{

    /**
     * @var \Spryker\Zed\StateMachine\Business\StateMachine\TimeoutInterface
     */
    protected $timeout;

    /**
     * @var \Spryker\Zed\StateMachine\Business\StateMachine\HandlerResolverInterface
     */
    protected $stateMachineHandlerResolver;

    /**
     * @var \Spryker\Zed\StateMachine\Business\StateMachine\PersistenceInterface
     */
    protected $stateMachinePersistence;

    /**
     * @var \Spryker\Zed\StateMachine\Persistence\StateMachineQueryContainerInterface
     */
    protected $stateMachineQueryContainer;

    /**
     * @param \Spryker\Zed\StateMachine\Business\StateMachine\TimeoutInterface $timeout
     * @param \Spryker\Zed\StateMachine\Business\StateMachine\HandlerResolverInterface $stateMachineHandlerResolver
     * @param \Spryker\Zed\StateMachine\Business\StateMachine\PersistenceInterface $stateMachinePersistence
     */
    public function __construct(
        TimeoutInterface $timeout,
        HandlerResolverInterface $stateMachineHandlerResolver,
        PersistenceInterface $stateMachinePersistence,
        StateMachineQueryContainerInterface $stateMachineQueryContainer
    ) {
        $this->timeout = $timeout;
        $this->stateMachineHandlerResolver = $stateMachineHandlerResolver;
        $this->stateMachinePersistence = $stateMachinePersistence;
        $this->stateMachineQueryContainer = $stateMachineQueryContainer;
    }

    /**
     * @param \Generated\Shared\Transfer\StateMachineItemTransfer[] $stateMachineItems
     * @param \Spryker\Zed\StateMachine\Business\Process\ProcessInterface[] $processes
     * @param string[] $sourceStates
     *
     * @throws \Exception
     *
     * @return void
     */
    public function updateStateMachineItemState(
        array $stateMachineItems,
        array $processes,
        array $sourceStates
    ) {

        if (count($stateMachineItems) === 0) {
            return;
        }

        $this->getConnection()->beginTransaction();

        try {
            foreach ($stateMachineItems as $stateMachineItemTransfer) {
                $this->assertStateMachineItemHaveRequiredData($stateMachineItemTransfer);

                $process = $processes[$stateMachineItemTransfer->getProcessName()];

                $this->assertSourceStateExists($sourceStates, $stateMachineItemTransfer);

                $sourceState = $sourceStates[$stateMachineItemTransfer->getIdentifier()];
                $targetState = $stateMachineItemTransfer->getStateName();

                $this->transitionState($sourceState, $targetState, $stateMachineItemTransfer, $process);
            }
        } catch (\Exception $e) {
            $this->getConnection()->rollBack();
            throw $e;
        }

        $this->getConnection()->commit();
    }

    /**
     * @return \Propel\Runtime\Connection\ConnectionInterface
     */
    protected function getConnection()
    {
        return $this->stateMachineQueryContainer->getConnection();
    }

    /**
     * @param \Generated\Shared\Transfer\StateMachineItemTransfer $stateMachineItemTransfer
     * @param string $sourceState
     * @param string $targetState
     *
     * @throws \Spryker\Zed\StateMachine\Business\Exception\StateMachineException
     * @return void
     */
    protected function assertTransitionAlreadyProcessed(
        StateMachineItemTransfer $stateMachineItemTransfer,
        $sourceState,
        $targetState
    ) {
        $isAlreadyTransitioned = $this->isAlreadyTransitioned($stateMachineItemTransfer);

        if ($isAlreadyTransitioned) {
            throw new StateMachineException(
                sprintf(
                    'Transition between "%s" -> "%s" already processed.',
                    $sourceState,
                    $targetState
                )
            );
        }
    }

    /**
     * @param \Generated\Shared\Transfer\StateMachineItemTransfer $stateMachineItemTransfer
     *
     * @return bool
     */
    protected function isAlreadyTransitioned(StateMachineItemTransfer $stateMachineItemTransfer)
    {
        $numberOfItems = $this->stateMachineQueryContainer->queryLastHistoryItem(
            $stateMachineItemTransfer,
            $stateMachineItemTransfer->getIdItemState()
        )->count();

        return $numberOfItems > 0;
    }

    /**
     * @param array $sourceStateBuffer
     * @param StateMachineItemTransfer $stateMachineItemTransfer
     *
     * @throws \Spryker\Zed\StateMachine\Business\Exception\StateMachineException
     *
     * @return void
     */
    protected function assertSourceStateExists(
        array $sourceStateBuffer,
        StateMachineItemTransfer $stateMachineItemTransfer
    ) {
        if (!isset($sourceStateBuffer[$stateMachineItemTransfer->getIdentifier()])) {
            throw new StateMachineException(
                sprintf('Could not update state, source state not found.')
            );
        }
    }

    /**
     * @param StateMachineItemTransfer $stateMachineItemTransfer
     *
     * @return void
     */
    protected function assertStateMachineItemHaveRequiredData(StateMachineItemTransfer $stateMachineItemTransfer)
    {
        $stateMachineItemTransfer->requireProcessName()
            ->requireStateMachineName()
            ->requireIdentifier()
            ->requireStateName();
    }

    /**
     * @param StateMachineItemTransfer $stateMachineItemTransfer
     *
     * @return void
     */
    protected function notifyHandlerStateChanged(StateMachineItemTransfer $stateMachineItemTransfer)
    {
        $stateMachineHandler = $this->stateMachineHandlerResolver->get($stateMachineItemTransfer->getStateMachineName());

        $stateMachineHandler->itemStateUpdated($stateMachineItemTransfer);
    }

    /**
     * @param ProcessInterface $process
     * @param string $sourceState
     * @param StateMachineItemTransfer $stateMachineItemTransfer
     *
     * @return void
     */
    protected function updateTimeouts(
        ProcessInterface $process,
        $sourceState,
        StateMachineItemTransfer $stateMachineItemTransfer
    ) {
        $this->timeout->dropOldTimeout($process, $sourceState, $stateMachineItemTransfer);
        $this->timeout->setNewTimeout($process, $stateMachineItemTransfer);
    }

    /**
     * @param string $sourceState
     * @param string $targetState
     * @param StateMachineItemTransfer $stateMachineItemTransfer
     * @param ProcessInterface $process
     *
     * @throws \Spryker\Zed\StateMachine\Business\Exception\StateMachineException
     *
     * @return void
     */
    protected function transitionState(
        $sourceState,
        $targetState,
        StateMachineItemTransfer $stateMachineItemTransfer,
        ProcessInterface $process
    ) {
        if ($sourceState === $targetState) {
            return;
        }
        $this->assertTransitionAlreadyProcessed($stateMachineItemTransfer, $sourceState, $targetState);
        $this->updateTimeouts($process, $sourceState, $stateMachineItemTransfer);
        $this->notifyHandlerStateChanged($stateMachineItemTransfer);
        $this->stateMachinePersistence->saveItemStateHistory($stateMachineItemTransfer);
    }


}
