<?php

namespace Messaging;

interface Adapter
{
    public function getMessageFactory();

    /**
     * @param Message $message
     * @throws FailedAcknowledgementException
     */
    public function acknowledge(Message $message);

    public function receive($limit = 1, $timeout = 0, callable $onTimeout = null);

    /**
     * Gets the message from the queue
     *
     * @param Message $message
     */
    public function dequeue(Message $mesaage);

    /**
     * Enqueues given $message
     *
     * @throws FailedEnqueueException
     */
    public function enqueue(Message $message, $timeout = 0, callable $onTimeout = null);
}
