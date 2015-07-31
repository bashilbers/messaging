<?php

namespace Messaging\Adapter\RabbitMQ;

use Messaging\Worker as IWorker;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Message\AMQPMessage;

/**
 * @author Sebastiaan Hilbers <bashilbers@gmail.com>
 */
class Worker implements IWorker
{
    use \Messaging\EventDispatcherTrait;
    use \Messaging\ConsumerTrait;

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

    public function work($maxMessages = null, $timeOut = null)
    {
        $callback = function (AMQPMessage $message) {
            $this->handle($message);
        };

        $this->channel->basic_qos(null, 1, null);
        $this->channel->basic_consume($this->queue, '', false, false, false, false, $callback);

        // Loop infinitely (or up to $count) to execute tasks
        while (count($this->channel->callbacks) && (is_null($maxMessages) || ($maxMessages > 0))) {
            $this->channel->wait();
            if (!is_null($maxMessages)) {
                $maxMessages--;
            }
        }
    }

    /**
     * Handles a message.
     *
     * @param mixed $message
     */
    public function handle(AMQPMessage $message)
    {
        /** @var AMQPChannel $channel */
        $channel = $message->delivery_info['channel'];

        // Listen to the "reply_to" queue
        $replyExchange = null;
        $replyQueue = null;
        if ($message->has('reply_to')) {
            list($replyExchange, $replyQueue) = explode(';', $message->get('reply_to'));
        }

        $this->trigger(IWorker::BEFORE_UNSERIALIZATION, [$message]);
        $envelope = unserialize($message->body);

        try {
            // Execute the handler; consume the message
            $this->trigger(IWorker::BEFORE_CONSUMING, [$envelope]);
            $this->consume($envelope);
            $this->trigger(IWorker::AFTER_CONSUMING, [$envelope]);

            $success = true;
            $e = null;
        } catch (\Exception $e) {
            $success = false;
        }

        // Signal the job status to RabbitMQ
        if ($success) {
            $channel->basic_ack($message->delivery_info['delivery_tag']);
        } else {
            $channel->basic_reject($message->delivery_info['delivery_tag'], false);
        }

        $dispatcherNotified = false;

        // Signal the job status to the dispatcher
        if ($replyExchange) {
            $message = ($success ? 'finished' : 'errored');
            $dispatcherNotified = $this->notifyDispatcher($replyExchange, $replyQueue, $message);
        }

        if ($success) {
            $this->trigger(IWorker::ON_HANDLER_SUCCESS, [$envelope, $dispatcherNotified]);
        } else {
            $this->trigger(IWorker::ON_HANDLER_ERROR, [$envelope, $e, $dispatcherNotified]);
        }
    }

    /**
     * Signal to the emitter of the message that we finished.
     *
     * @param string $exchange
     * @param string $queue
     * @param string $messageContent Message to send to the dispatcher.
     *
     * @return bool
     */
    protected function notifyDispatcher($exchange, $queue, $messageContent)
    {
        // We put in the queue that we finished
        $this->channel->basic_publish(new AMQPMessage($messageContent), $exchange);

        // Read the first message coming out of the queue
        $message = $this->waitForMessage($queue, 0.5);
        if (!$message) {
            // Shouldn't happen -> error while delivering messages?
            return false;
        }

        // If the first message of the queue is our message, we can die in peace
        // (else it would be the "timeout" message from the dispatcher)
        $dispatcherNotified = ($message->body == $messageContent);

        // Delete our queue
        $this->channel->queue_delete($queue);

        return $dispatcherNotified;
    }

    /**
     * Read a queue until there's a message or until a timeout.
     *
     * @param string $queue
     * @param int    $timeout Time to wait in seconds
     * @return AMQPMessage|null
     */
    protected function waitForMessage($queue, $timeout)
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

    public function __invoke()
    {
        return call_user_func_array([$this, 'work'], func_get_args());
    }
}
