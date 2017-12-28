<?php

namespace Scheduler;

use Scheduler\Job\JobInterface;
use DateTimeInterface;

/**
 * Interface Scheduler
 * @package Scheduler
 */
interface SchedulerInterface
{
    /**
     * Get actions to be executed between given dates
     * Actions are ordered by start time
     *
     * @param DateTimeInterface $from
     * @param DateTimeInterface|null $to - if not given then 'now'
     * @param bool $inc - include boundary values (time of action executions is equals to $from or $to)
     * @return \Iterator
     */
    public function getIterator(DateTimeInterface $from, DateTimeInterface $to = null, $inc = false);

    /**
     * @param JobInterface $job
     * @return mixed
     */
    public function addJob(JobInterface $job);

}