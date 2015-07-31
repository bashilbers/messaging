<?php

namespace Messaging;

/**
 * @author Sebastiaan Hilbers <bashilbers@gmail.com>
 */
interface Consumer
{
    const BEFORE_UNSERIALIZATION = 'before-unserialization';
    const BEFORE_CONSUMING       = 'before-consuming';
    const AFTER_CONSUMING        = 'after-consuming';
    const ON_WORKER_SUCCESS      = 'on-handler-success';
    const ON_WORKER_ERROR        = 'on-handler-error';

    public function addWorker(callable $worker);

    /**
     * @param integer|null $limit the limit of messages to fetch
     * @param integer|null $timeoud The timeout limit
     */
    public function receive($limit = 1, $timeout = -1);

    public function handle(Message $message);
}
