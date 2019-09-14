<?php

namespace AlexS\Guzzle;

class ExecutionFlow
{
    /** @var TimelineEntry */
    public $currentEntry;

    public function isFree(): bool
    {
        return !$this->currentEntry || $this->currentEntry->isClosed();
    }
}
