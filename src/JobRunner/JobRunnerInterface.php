<?php

namespace Scheduler\JobRunner;

use Scheduler\SchedulerInterface;
use DateTimeInterface;

/**
 * Interface JobRunnerInterface
 * @package Scheduler\TaskRunner
 */
interface JobRunnerInterface
{
    /**
     * @param SchedulerInterface $scheduler
     * @param DateTimeInterface $from
     * @param DateTimeInterface|null $to - `now` by default
     * @param bool $inc - include boundary values (time of action executions is equals to $from or $to)
     * @return mixed
     */
    public function run(SchedulerInterface $scheduler, DateTimeInterface $from, DateTimeInterface $to = null, $inc = true);
}