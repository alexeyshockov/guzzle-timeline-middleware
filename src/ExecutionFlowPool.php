<?php

namespace AlexS\Guzzle;

class ExecutionFlowPool
{
    /** @var ExecutionFlow[] */
    private $flows = [];

    private function getFree(): int
    {
        foreach ($this->flows as $k => $executor) {
            if ($executor->isFree()) {
                return $k;
            }
        }

        $this->flows[] = new ExecutionFlow();

        return count($this->flows) - 1;
    }

    public function assign(ExecutionFlowTimelineEntry $entry): ExecutionFlowTimelineEntry
    {
        return $this->flows[$n = $entry->flow = $this->getFree()]->currentEntry = $entry;
    }
}
