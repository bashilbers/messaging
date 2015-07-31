<?php

namespace Messaging\Tests\Fixtures;

class UserObject implements \Serializable
{
    protected $id;

    protected $firstName;

    protected $lastName;

    public function __construct($id, $first, $last)
    {
        $this->id = $id;
        $this->firstName = $first;
        $this->lastName = $last;
    }

    public function serialize()
    {
        return serialize([
            'id' => $this->id,
            'firstName' => $this->firstName,
            'lastName' => $this->lastName
        ]);
    }

    public function unserialize($serialized)
    {
        $unserialized = unserialize($serialized);
        $this->id = $unserialized['id'];
        $this->firstName = $unserialized['firstName'];
        $this->lastName = $unserialized['lastName'];
    }

}