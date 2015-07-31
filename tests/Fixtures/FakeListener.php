<?php

namespace Messaging\Tests\Fixtures;

class FakeListener
{
    public function beforeTaskUnserialization($msg)
    {

    }

    public function beforeTaskExecution(\Messaging\Message $message)
    {

    }

    public function afterTaskExecution(\Messaging\Message $message)
    {

    }

    public function onTaskSuccess(\Messaging\Message $message)
    {

    }

    public function onTaskError(\Messaging\Message $message, \Exception $e, $dispatcherNotified)
    {

    }
}