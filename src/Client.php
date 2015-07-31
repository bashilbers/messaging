<?php

namespace Messaging;

class Client implements Consumer, Producer
{
    protected $workers = [];

    public function __construct(\Adapter $adapter)
    {
        $this->adapter = $adapter;
    }

    public function addWorker(callable $worker)
    {
        $this->workers[] = $worker;
    }

    /**
     * @param integer|null $limit the limit of messages to fetch
     * @param integer|null $timeoud The timeout limit
     */
    public function receive($limit = 1, $timeout = -1)
    {
        // TODO: Implement receive() method.
    }

    public function handle(Message $message)
    {
        // TODO: Implement handle() method.
    }

    /**
     * @param $payload
     * @param array $options
     * @return Message
     */
    public function create($payload, array $options = [])
    {
        // TODO: Implement create() method.
    }

    /**
     * @param Message[] $messages
     */
    public function send(array $messages)
    {
        // TODO: Implement send() method.
    }
}
