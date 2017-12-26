<?php

namespace Scheduler;

use Scheduler\Task\TaskInterface;

/**
 * Interface Scheduler
 * @package Scheduler
 */
interface SchedulerInterface
{
    /**
     * Get Tasks to be executed between given dates
     * Tasks are ordered by start time
     *
     * @param \DateTime $from
     * @param \DateTime|null $to - if not given then 'now'
     * @param bool $inc - include boundary values (time of task executions is equals to $from or $to)
     * @return \Iterator
     */
    public function getIterator(\DateTime $from, \DateTime $to = null, bool $inc = false):\Iterator;

    /**
     * @param TaskInterface $task
     * @return mixed
     */
    public function addTask(TaskInterface $task);

}