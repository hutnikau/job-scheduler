<?php

namespace Scheduler\JobRunner;

use Scheduler\Job\JobInterface;

/**
 * Class Report
 * @package Scheduler\TaskRunner
 * @author Aleh Hutnikau, <goodnickoff@gmail.com>
 */
class Report
{
    /** @var JobInterface executed task */
    private $task;

    /** @var mixed Execution result (task return value) */
    private $result;

    public function __construct(JobInterface $task, $result)
    {
        $this->task = $task;
        $this->result = $result;
    }

    /**
     * @return mixed
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * @return JobInterface
     */
    public function getTask():JobInterface
    {
        return $this->task;
    }
}