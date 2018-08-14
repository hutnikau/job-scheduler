<?php

namespace Scheduler\Action;

use DateTimeInterface;
use Scheduler\Exception\SchedulerException;
use Scheduler\Job\JobInterface;

/**
 * Interface ActionInterface
 * @package Scheduler\Action
 */
interface ActionInterface
{
    const STATE_INITIAL = 0;
    const STATE_IN_PROGRESS = 1;
    const STATE_FINISHED = 2;

    /**
     * @return mixed
     */
    public function __invoke();

    /**
     * Get time of occurrence. Note that this is not necessary time of execution.
     * @return DateTimeInterface
     */
    public function getTime();

    /**
     * Related job
     * @return JobInterface
     */
    public function getJob();

    /**
     * @return mixed
     */
    public function getId();

    /**
     * @return integer
     */
    public function getState();

    /**
     * Get action report.
     * @throws SchedulerException in case if action is not in finished state.
     * @return mixed
     */
    public function getReport();
}