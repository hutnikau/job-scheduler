<?php

namespace Scheduler\Job;

use Recurr\Rule;

/**
 * Interface JobInterface
 * @package Job
 */
interface JobInterface
{
    /**
     * @return mixed
     */
    public function getRRule():Rule;

    /**
     * @return callable
     */
    public function getCallable():callable;
}