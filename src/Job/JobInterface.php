<?php

namespace Scheduler\Job;

/**
 * Interface JobInterface
 * @package Job
 */
interface JobInterface
{
    /**
     * @return RRule
     */
    public function getRRule();

    /**
     * @return callable
     */
    public function getCallable();
}