<?php

namespace AlexS\Guzzle;

use GuzzleHttp\TransferStats;
use Psr\Http\Message\UriInterface;
use function GuzzleHttp\Psr7\uri_for;

class TimelineEntry
{
    /** @var UriInterface */
    public $uri;
    /** @var int */
    public $start;
    /** @var null|int */
    public $end;

    public function __construct($uri)
    {
        $this->uri = uri_for($uri);
        $this->start = round(microtime(true) * 1000);
    }

    public function close(): void
    {
        !$this->end && $this->end = round(microtime(true) * 1000);
    }

    public function isOpen(): bool
    {
        return $this->end === null;
    }

    public function isClosed(): bool
    {
        return $this->end !== null;
    }

    public function onStats(TransferStats $stats): void
    {
        // We can do something here...
    }
}
