<?php

namespace Scheduler\Task;

use Recurr\Rule;

/**
 * Class CallableTask
 * @package Task
 * @author Aleh Hutnikau, <goodnickoff@gmail.com>
 */
class CallableTask extends AbstractTask
{
    /**
     * @var Rule
     */
    protected $rRule;

    /**
     * @var
     */
    protected $callable;

    /**
     * @return mixed|void
     */
    public function __invoke()
    {
        return call_user_func($this->callable);
    }

    /**
     * AbstractTask constructor.
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
    public function getRRule():Rule
    {
        return $this->rRule;
    }
}