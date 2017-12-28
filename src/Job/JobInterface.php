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
     * @return Rule
     */
    public function getRRule();

    /**
     * @return callable
     */
    public function getCallable();
}