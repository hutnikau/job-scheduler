<?php

namespace Scheduler\ActionInspector;

use Scheduler\Action\ActionInterface;

/**
 * Action inspector is used to track all the actions which are in progress or in finished state in order to be able
 * to distribute actions between several workers and avoid action to be performed twice.
 *
 * Interface ActionInspectorInterface
 * @package Scheduler\ActionLog
 */
interface ActionInspectorInterface
{
    /**
     * Update action state. If action in the same state is already exists in the log
     * or transaction to given state is not allowed (for example back from finished to in progress)
     * then method returns `false` what means that action has been already taken or finished by another worker.
     *
     * Allowed states flow:
     * initial -> in progress -> finished (see ActionInterface state constants)
     *
     * @param ActionInterface $action
     * @return boolean returns `false` if action is already exists in this state in the log
     */
    public function update(ActionInterface $action);
}