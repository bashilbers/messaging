<?php

namespace Messaging\Message;

/**
 * @author Sebastiaan Hilbers <bashilbers@gmail.com>
 */
interface RuntimeLimitable
{
    /**
     * @return int seconds
     */
    public function getRuntimeLimit();
}
