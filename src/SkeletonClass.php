<?php

namespace JobScheduler;

/**
 * Class Task
 * @package JobScheduler
 * @author Aleh Hutnikau, <goodnickoff@gmail.com>
 *

Entities:

Task
 - __construct($rrule)
 - __invoke()
TaskRunner
 - run(Scheduler $schedule)
TaskIterator
 - current()
Scheduler
 - getIterator()
 - add(Task $task)
Storage
 - get()
 - set($list)



arrayConfig:
[
  'rrule' => 'FREQ=DAILY;UNTIL=20171231T000000;BYDAY=MO,TU',
  'callable' =>
]

run:
from command line (where to store config?)
from php code (instantiate worker). Config may be injected to the worker

initialization:
crete tables in the storage?

storages:
RDS, KV?

new Scheduler($storage);

 *
 *
 *
 *
 */
class Task
{
    /**
     * Create a new Skeleton Instance
     */
    public function __construct()
    {
        // constructor body
    }

    /**
     * Friendly welcome
     *
     * @param string $phrase Phrase to return
     *
     * @return string Returns the phrase passed in
     */
    public function echoPhrase($phrase)
    {
        return $phrase;
    }
}
