<?php

namespace Messaging;

/**
 * @author Sebastiaan Hilbers <bashilbers@gmail.com>
 */
abstract class AbstractWorker
{
    /**
     * @param Message $message
     */
    abstract public function execute(Message $message);

    /**
     * @param Message $message
     */
    public function __invoke(Message $message)
    {
        return $this->work($message);
    }
}
