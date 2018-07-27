<?php

namespace Scheduler\ActionInspector;

use Scheduler\Action\ActionInterface;
use Scheduler\Exception\SchedulerException;

/**
 * Class FileActionInspector
 * @package Scheduler\ActionInspector
 * @author Aleh Hutnikau, <goodnickoff@gmail.com>
 */
class FileActionInspector extends AbstractActionInspector
{
    /** @var string */
    private $filePath;

    /** @var resource */
    private $fh;

    /**
     * FileActionLog constructor.
     * @param $filePath
     */
    public function __construct($filePath)
    {
        $this->filePath = $filePath;
        if (!file_exists($this->filePath)) {
            file_put_contents($this->filePath, '');
        }
        $this->fh = fopen($this->filePath, 'r+');
    }

    /**
     * @param ActionInterface $action
     * @return boolean returns `false` if action is already exists in this state in the log
     * @throws SchedulerException
     */
    public function update(ActionInterface $action)
    {
        flock($this->fh, LOCK_EX);
        fseek($this->fh, 0);
        $content = '';
        $messages = [];
        while (!feof($this->fh)) {
            $content .= fgets($this->fh);
        }
        $actionId = $action->getId();
        $actionState = $action->getState();
        if ($content) {
            $messages = json_decode($content, true);
        }
        if (!isset($messages[$actionId])) {
            $messages[$actionId] = ['state' => $actionState];
            $result = true;
        } else if ($this->isStateAllowed($action, $messages[$actionId]['state'])){
            $messages[$actionId]['state'] = $actionState;
            if ($actionState === ActionInterface::STATE_FINISHED) {
                $messages[$actionId]['report'] = $action->getReport();
            }
            $result = true;
        } else {
            $result = false;
        }
        fseek($this->fh, 0);
        fwrite($this->fh, json_encode($messages));
        flock($this->fh, LOCK_UN);
        return $result;
    }

    function __destruct()
    {
        fclose($this->fh);
    }
}