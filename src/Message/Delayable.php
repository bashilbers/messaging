<?php

namespace Messaging\Message;

/**
 * @author Sebastiaan Hilbers <bashilbers@gmail.com>
 */
interface Delayable
{
    /**
     * @return int seconds
     */
    public function getDelayInSeconds();

    /**
     * @return \Datetime
     */
    public function delayedUntil();
}
