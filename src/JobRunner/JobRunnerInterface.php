<?php

namespace Scheduler\JobRunner;

use Scheduler\SchedulerInterface;

/**
 * Interface JobRunnerInterface
 * @package Scheduler\TaskRunner
 */
interface JobRunnerInterface
{
    /**
     * @param SchedulerInterface $scheduler
     * @return mixed
     */
    public function run(SchedulerInterface $scheduler);
}