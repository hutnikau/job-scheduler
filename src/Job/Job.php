<?php

namespace Scheduler\Job;

use Recurr\Rule;

/**
 * Class Job
 * @package Job
 * @author Aleh Hutnikau, <goodnickoff@gmail.com>
 */
class Job implements JobInterface
{
    /** @var Rule */
    private $rRule;

    /** @var callable */
    private $callable;

    /**
     * Job constructor.
     * @param Rule $rRule - recurrence rules (@see https://github.com/simshaun/recurr)
     * @param callable $callable
     */
    public function __construct(Rule $rRule, callable $callable)
    {
        $this->rRule = $rRule;
        $this->callable = $callable;
    }

    /**
     * @return Rule
     */
    public function getRRule()
    {
        return $this->rRule;
    }

    /**
     * @return callable
     */
    public function getCallable()
    {
        return $this->callable;
    }
}