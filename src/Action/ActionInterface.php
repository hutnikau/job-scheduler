<?php

namespace Scheduler\Action;

use DateTimeInterface;
use Scheduler\Job\JobInterface;

/**
 * Interface ActionInterface
 * @package Scheduler\Action
 */
interface ActionInterface
{
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
}