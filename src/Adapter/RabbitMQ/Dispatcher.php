<?php

namespace Messaging\Adapter\RabbitMQ;

use Messaging\SynchronousDispatcher;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Message\AMQPMessage;

/**
 * @author Sebastiaan Hilbers <bashilbers@gmail.com>
 */
class Dispatcher implements SynchronousDispatcher
{
    use \Messaging\EventDispatcherTrait;

    /**
     * @var AMQPChannel
     */
    protected $channel;

    /**
     * @var string
     */
    protected $queue;

    /**
     * @param AMQPChannel $channel
     * @param string      $queue
     */
    public function __construct(AMQPChannel $channel, $queue)
    {
        $this->channel = $channel;
        $this->queue = $queue;
    }

    public function send(\Messaging\Message $message)
    {
        $this->trigger(self::BEFORE_SERIALIZATION, [$message]);

        $messageOptions = [
            'delivery_mode' => 2, // make message persistent
        ];

        $rmm = new AMQPMessage(serialize($message), $messageOptions);

        $this->trigger(self::BEFORE_DISPATCH, [$message]);

        $this->channel->basic_publish($rmm, '', $this->queue);
    }

    /**
     * @param \Messaging\Message $message
     * @param int $wait
     * @param callable $completed
     * @param callable $timedout
     * @param callable $errored
     */
    public function sendAndWait(\Messaging\Message $message, $wait = 5, callable $completed = null, callable $timedout = null, callable $errored = null)
    {
        $this->trigger(self::BEFORE_SERIALIZATION, [$message]);

        $waitForResult = ($wait > 0);
        $messageOptions = [
            'delivery_mode' => 2, // make message persistent
        ];

        $replyExchange = null;
        $replyQueue = null;

        if ($waitForResult) {
            // Create a temporary exchange (durable, autodelete) for communicating with the worker
            $replyExchange = uniqid('tmp');
            $this->channel->exchange_declare($replyExchange, 'fanout', false, true, true);

            // Create and bind a queue for the dispatcher (our queue) (exclusive queue)
            list($replyQueue, , ) = $this->channel->queue_declare('', false, false, true);
            $this->channel->queue_bind($replyQueue, $replyExchange);

            // Create and bind a queue for the worker (durable non-exclusive queue)
            list($workerReplyQueue, , ) = $this->channel->queue_declare('', false, true, false);
            $this->channel->queue_bind($workerReplyQueue, $replyExchange);
            $messageOptions['reply_to'] = $replyExchange . ';' . $workerReplyQueue;
        }

        $amqpMessage = new AMQPMessage(serialize($message), $messageOptions);

        $this->trigger(self::BEFORE_DISPATCH, [$message, $amqpMessage]);
        $this->channel->basic_publish($amqpMessage, '', $this->queue);

        if ($waitForResult) {
            $this->waitForTask($wait, $replyExchange, $replyQueue, $completed, $timedout, $errored);
        }
    }

    /**
     * @param $timeout
     * @param $exchange
     * @param $queue
     * @param callable $completed
     * @param callable $timedout
     * @param callable $errored
     */
    private function waitForTask(
        $timeout,
        $exchange,
        $queue,
        callable $completed = null,
        callable $timedout = null,
        callable $errored = null
    ) {
        // Wait X seconds for the task to be finished
        $message = $this->waitForMessage($queue, $timeout);

        // No response from the worker (the task is not finished)
        if (! $message) {
            // We put in the queue that we timed out
            $this->channel->basic_publish(new AMQPMessage('timeout'), $exchange);

            // Read the first message coming out of the queue
            $message = $this->waitForMessage($queue, 0.5);
            if (!$message) {
                // Shouldn't happen -> error while delivering messages?
                return;
            }
        }

        // If the first message of the queue is a "finished" message from the worker
        if ($message->body == 'finished') {
            if ($completed !== null) {
                call_user_func($completed);
            }

            // Delete the temporary exchange
            $this->channel->exchange_delete($exchange);
        }

        // If the first message of the queue is a "errored" message from the worker
        if ($message->body == 'errored') {
            if ($errored !== null) {
                $e = new \RuntimeException("An error occured in the background task");
                call_user_func($errored, $e);
            }

            // Delete the temporary exchange
            $this->channel->exchange_delete($exchange);
        }

        // If the first message of the queue is our "timeout" message
        if ($message->body == 'timeout') {
            if ($timedout !== null) {
                call_user_func($timedout);
            }

            // Do not delete the temp exchange: still used by the worker
        }

        // Delete the temporary queue
        $this->channel->queue_delete($queue);
    }

    /**
     * Read a queue until there's a message or until a timeout.
     *
     * @param string $queue
     * @param int    $timeout Time to wait in seconds
     * @return AMQPMessage|null
     */
    private function waitForMessage($queue, $timeout)
    {
        $timeStart = microtime(true);
        do {
            // Get message and auto-ack
            $response = $this->channel->basic_get($queue);
            if ($response) {
                return $response;
            }

            // Sleep 300 ms
            usleep(300000);
            $timeSpent = microtime(true) - $timeStart;
        } while ($timeSpent < $timeout);

        return null;
    }
}
