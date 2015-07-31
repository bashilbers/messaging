<?php

namespace Messaging;

/**
 * Waits for tasks that are run in the background
 *
 * @author Sebastiaan Hilbers <bashilbers@gmail.com>
 */
interface SynchronousProducer extends Producer
{
    /**
     * Run a task in background.
     *
     * You can use $wait to wait a given time for the task to complete.
     * If the task hasn't finished during this time, $timedout will be called and this method will return.
     * If the task has finished, $completed will be called.
     *
     * @param Message  $message
     * @param int      $wait      Number of seconds to wait for the task to complete. If 0, doesn't wait.
     * @param callable $completed Called (if $wait > 0) when the task has completed.
     * @param callable $timedout  Called (if $wait > 0) if we hit the timeout while waiting.
     * @param callable $errored   Called (if $wait > 0) if the task errors. Takes 1 parameter which is the exception.
     *
     * @return void No results
     */
    public function sendAndWait(
        Message $message,
        $wait = 5,
        callable $completed = null,
        callable $timedout = null,
        callable $errored = null
    );
}
