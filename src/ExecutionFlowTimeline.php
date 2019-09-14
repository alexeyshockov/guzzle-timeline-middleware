<?php

namespace AlexS\Guzzle;

use Generator;
use Psr\Http\Message\UriInterface;

class ExecutionFlowTimeline extends Timeline
{
    /** @var ExecutionFlowPool */
    private $pool;

    public function __construct(string $fileName, ?UriInterface $baseUri = null)
    {
        parent::__construct($fileName, $baseUri);

        $this->pool = new ExecutionFlowPool();
    }

    public function addEntry($uri): ExecutionFlowTimelineEntry
    {
        return $this->pool->assign($this->entries[] = new ExecutionFlowTimelineEntry($uri));
    }

    protected function jsonData(): Generator
    {
        foreach (parent::jsonData() as $k => $row) {
            yield [(string)($this->entries[$k]->flow + 1)] + $row;
        }
    }
}
