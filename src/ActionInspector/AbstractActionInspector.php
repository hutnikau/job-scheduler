<?php

namespace Scheduler\ActionInspector;

use Scheduler\Action\ActionInterface;

/**
 * Class AbstractActionInspector
 *
 * @package Scheduler\ActionInspector
 * @author Aleh Hutnikau, <goodnickoff@gmail.com>
 */
abstract class AbstractActionInspector implements ActionInspectorInterface
{
    protected $statesFlow = [
        ActionInterface::STATE_INITIAL => [ActionInterface::STATE_IN_PROGRESS, ActionInterface::STATE_FINISHED],
        ActionInterface::STATE_IN_PROGRESS => [ActionInterface::STATE_FINISHED],
        ActionInterface::STATE_FINISHED => [],
    ];

    /**
     * @param ActionInterface $action
     * @param $prevState
     * @return boolean
     */
    protected function isStateAllowed(ActionInterface $action, $prevState)
    {
        return in_array($action->getState(), $this->statesFlow[$prevState]);
    }
}