<?php

namespace AlexS\Guzzle;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;
use function GuzzleHttp\Promise\rejection_for;
use function GuzzleHttp\Psr7\uri_for;

/**
 * @api
 *
 * @see https://developers.google.com/chart/interactive/docs/gallery/timeline
 */
class TimelineReporter
{
    /** @var string */
    protected $fileName;
    /** @var null|int */
    protected $maxReportSize;
    /** @var Timeline */
    protected $report;
    /** @var int */
    protected $reportNumber = 1;
    /** @var null|UriInterface */
    protected $baseUri;

    /**
     * Guzzle middleware
     */
    public static function middleware(string $fileName, $baseUri = null, ?int $maxReportSize = null): callable
    {
        $reporter = new static($fileName, $baseUri ? uri_for($baseUri) : null, $maxReportSize);

        return static function (callable $nextHandler) use ($reporter) {
            return static function (RequestInterface $request, array $options) use ($nextHandler, $reporter) {
                $entry = $reporter->addEntry($request->getUri());

                // TODO Check for previous value and combine
                $options['on_stats'] = [$entry, 'onStats'];

                return $nextHandler($request, $options)->then(
                    static function ($result) use ($entry) {
                        $entry->close();

                        return $result;
                    },
                    static function ($error) use ($entry) {
                        $entry->close();

                        return rejection_for($error);
                    }
                );
            };
        };
    }

    protected function __construct(string $fileName, ?UriInterface $baseUri, ?int $maxReportSize)
    {
        $this->fileName = $fileName;
        $this->baseUri = $baseUri;
        $this->maxReportSize = $maxReportSize;

        $this->report = $this->createReport($this->reportNumber);
    }

    protected function getReportFileName(int $reportNumber): string
    {
        return strtr($this->fileName, ['${NUMBER}' => $reportNumber]);
    }

    protected function createReport(int $reportNumber): Timeline
    {
        return new Timeline($this->getReportFileName($reportNumber), $this->baseUri);
    }

    protected function addEntry($uri): TimelineEntry
    {
        if ($this->maxReportSize && count($this->report) >= $this->maxReportSize) {
            $this->report = $this->createReport(++$this->reportNumber);
        }

        return $this->report->addEntry($uri);
    }
}
