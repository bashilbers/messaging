<?php

namespace Messaging\Message;

/**
 * @author Sebastiaan Hilbers <bashilbers@gmail.com>
 */
interface Priorityable
{
    /**
     * @return int
     */
    public function getPriority();
}
