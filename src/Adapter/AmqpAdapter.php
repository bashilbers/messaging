<?php

namespace Messaging\Adapter;

use Messaging\Adapter;
use Messaging\FailedAcknowledgementException;
use Messaging\FailedEnqueueException;
use Messaging\Iterator;
use Messaging\MessageFactoryInterface;
use Messaging\MessageInterface;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Message\AMQPMessage;
use Messaging\Message;
use Messaging\Message\Delayable;

class AmqpAdapter implements Adapter
{
    /**
     * @var AMQPExchange
     */
    protected $exchange;

    protected $eventDispatcher;

    /**
     * @param AMQPChannel $channel
     * @param string      $queue
     */
    public function __construct(AMQPExchange $exchange)
    {
        $this->exchange = $exchange;
    }

    public function setEventDispatcher($dispatcher)
    {
        $this->eventDispatcher = $dispatcher;

        return $this;
    }

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
        if ($message instanceof Delayable) {
            if ($message->delayedUntil() < new \DateTime('now')) {
                throw new \OutOfBoundsException('Cannot enqueue messages in the past');

                // do something
            }
        }

        $body  = $message->getBody();
        $route = $message->getRoutingKey();
        $props = $message->getHeaders();

        return $this->exchange->publish($body, $route, $flags, $props);
    }
}
