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
     */
    public function __construct(Rule $rRule, callable $callabele)
    {
        $this->rRule = $rRule;
        $this->callable = $callabele;
    }

    /**
     * @return Rule
     */
    public function getRRule():Rule
    {
        return $this->rRule;
    }

    /**
     * @return callable
     */
    public function getCallable(): callable
    {
        return $this->callable;
    }
}