<?php

namespace SchedulerTests\ActionInspector\Samples;

use Scheduler\Job\RRule;
use Scheduler\Action\CallableAction;
use Scheduler\Action\ActionInterface;
use Scheduler\Job\Job;

/** @var int $start */
$rule = new RRule('FREQ=MINUTELY;COUNT=5', \DateTime::createFromFormat('U', $start, new \DateTimeZone('UTC')));
$job = new Job($rule, '\SchedulerTests\ActionInspector\Samples\my_callback_function');
$actions = [];
for ($i = 0; $i < 20; $i++) {
    $actions[] = new CallableAction($job, new \DateTime('@' . ($start + $i)));
}
function my_callback_function(ActionInterface $action) {
    return 'success';
}
