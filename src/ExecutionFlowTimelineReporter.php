<?php

namespace AlexS\Guzzle;

/**
 * @api
 *
 * @see https://developers.google.com/chart/interactive/docs/gallery/timeline
 */
class ExecutionFlowTimelineReporter extends TimelineReporter
{
    protected function createReport(int $reportNumber): Timeline
    {
        return new ExecutionFlowTimeline($this->getReportFileName($reportNumber), $this->baseUri);
    }
}
