<?php

namespace Scheduler\Task;

use Recurr\Rule;

/**
 * Class AbstractTask
 * @package Task
 * @author Aleh Hutnikau, <goodnickoff@gmail.com>
 */
abstract class AbstractTask implements TaskInterface
{
    /**
     * @var Rule
     */
    protected $rRule;

    /**
     * AbstractTask constructor.
     * @param Rule $rRule - recurrence rules (@see https://github.com/simshaun/recurr)
     */
    public function __construct(Rule $rRule)
    {
        $this->rRule = $rRule;
    }

    /**
     * @return Rule
     */
    public function getRRule()
    {
        return $this->rRule;
    }
}