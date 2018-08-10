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
     * FileActionInspector constructor.
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
        $messages = $this->getMessages();
        $actionId = $action->getId();
        $actionState = $action->getState();
        $previousState = isset($messages[$actionId]) ? $messages[$actionId]['state'] : null;

        if ($this->isStateAllowed($action, $previousState)) {
            $messages[$actionId]['state'] = $actionState;
            $result = true;
            if ($actionState === ActionInterface::STATE_FINISHED) {
                $messages[$actionId]['report'] = $action->getReport();
            }
        } else {
            $result = false;
        }

        $this->save($messages);
        flock($this->fh, LOCK_UN);
        return $result;
    }

    /**
     * Close an open file pointer when class instance destructs.
     */
    public function __destruct()
    {
        fclose($this->fh);
    }

    /**
     * Get action messages from log file
     * @return array
     */
    private function getMessages()
    {
        $messages = [];
        $content = '';
        fseek($this->fh, 0);
        while (!feof($this->fh)) {
            $content .= fgets($this->fh);
        }
        if ($content) {
            $messages = json_decode($content, true);
        }
        return $messages;
    }

    /**
     * Save action messages to file
     * @param array $messages
     */
    private function save(array $messages)
    {
        fseek($this->fh, 0);
        fwrite($this->fh, json_encode($messages));
    }
}