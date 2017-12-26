<?php

namespace Scheduler\TaskRunner;

use Scheduler\SchedulerInterface;

/**
 * Interface TaskRunner
 * @package Scheduler\TaskRunner
 */
interface TaskRunnerInterface
{
    /**
     * @param SchedulerInterface $scheduler
     * @return mixed
     */
    public function run(SchedulerInterface $scheduler);
}