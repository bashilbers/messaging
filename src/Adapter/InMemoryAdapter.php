<?php

namespace Messaging\Adapter;

use Messaging\Adapter;
use Messaging\FailedAcknowledgementException;
use Messaging\FailedEnqueueException;
use Messaging\Message;

/**
 * @author Sebastiaan Hilbers <bashilbers@gmail.com>
 */
class InMemoryAdapter implements Adapter
{
    public function getMessageFactory()
    {
        // TODO: Implement getMessageFactory() method.
    }

    /**
     * @param Message $message
     * @throws FailedAcknowledgementException
     */
    public function acknowledge(Message $message)
    {
        // TODO: Implement acknowledge() method.
    }

    public function receive($limit = 1, $timeout = 0, callable $onTimeout = null)
    {
        // TODO: Implement receive() method.
    }

    /**
     * Gets the message from the queue
     *
     * @param Message $message
     */
    public function dequeue(Message $mesaage)
    {
        // TODO: Implement dequeue() method.
    }

    /**
     * Enqueues given $message
     *
     * @throws FailedEnqueueException
     */
    public function enqueue(Message $message, $timeout = 0, callable $onTimeout = null)
    {
        // TODO: Implement enqueue() method.
    }
}
