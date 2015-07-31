<?php

namespace Messaging;

/**
 * @author Sebastiaan Hilbers <bashilbers@gmail.com>
 */
interface Producer
{
    const BEFORE_SERIALIZATION = 'before-serialization';
    const ON_CREATE            = 'on-create';
    const BEFORE_SEND          = 'before-send';

    /**
     * @param $payload
     * @param array $options
     * @return Message
     */
    public function create($payload, array $options = []);

    /**
     * @param Message[] $messages
     */
    public function send(array $messages);
}
