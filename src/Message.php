<?php

namespace Messaging;

/**
 * @author Sebastiaan Hilbers <bashilbers@gmail.com>
 */
final class Message
{
    protected $id;

    protected $headers = [];

    protected $body;

    protected $routingKey;

    protected $createdOn;

    public function __construct($body, array $headers = [], $id = null, $routingKey = null)
    {
        $this->id = $id;
        $this->headers = $headers;
        $this->body = $body;
        $this->routingKey = $routingKey;

        $this->createdOn = new \DateTime();
    }

    /**
     * Resets id
     */
    public function __clone()
    {
        $this->id = null;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    public function getHeaders()
    {
        return $this->headers;
    }

    public function getRoutingKey()
    {
        return $this->routingKey;
    }

    /**
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }
}
