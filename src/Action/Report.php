<?php

namespace Scheduler\Action;

/**
 * Class Report
 * @package Scheduler\Action
 * @author Aleh Hutnikau, <goodnickoff@gmail.com>
 */
class Report
{
    /** @var ActionInterface executed task */
    private $action;

    /** @var mixed Execution result (task return value) */
    private $result;

    /** @var string 'success'|'error'*/
    private $type;

    const TYPE_SUCCESS = 'success';
    const TYPE_ERROR = 'error';

    /**
     * Report constructor.
     * @param ActionInterface $action
     * @param mixed $result - action execution result or thrown exception in case of error
     * @param string $type
     */
    public function __construct(ActionInterface $action, $result, $type = self::TYPE_SUCCESS)
    {
        $this->action = $action;
        $this->result = $result;
        $this->type = $type;
    }

    /**
     * Action execution result or thrown exception in case of error
     * @return mixed
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * @return ActionInterface
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }
}