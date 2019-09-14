<?php

namespace AlexS\Guzzle;

use Countable;
use Generator;
use GuzzleHttp\Psr7\UriResolver;
use JsonSerializable;
use Psr\Http\Message\UriInterface;

class Timeline implements JsonSerializable, Countable
{
    public const TEMPLATE = __DIR__ . '/template-timeline.html';

    /** @var string */
    protected $fileName;
    /** @var TimelineEntry[] */
    protected $entries = [];
    /** @var null|UriInterface */
    protected $baseUri;

    public function __construct(string $fileName, ?UriInterface $baseUri = null)
    {
        $this->fileName = $fileName;
        $this->baseUri = $baseUri;
    }

    public function getBaseUri(): ?UriInterface
    {
        // TODO longest_common_uri()
        return $this->baseUri;
    }

    protected function jsonData(): Generator
    {
        $baseUri = $this->getBaseUri();
        foreach ($this->entries as $k => $entry) {
            if ($entry->isOpen()) {
                continue;
            }

            yield $k => [
                (string)($baseUri ? UriResolver::relativize($baseUri, $entry->uri) : $entry->uri),
                (string)($baseUri ? UriResolver::relativize($baseUri, $entry->uri) : $entry->uri),
                $entry->start,
                $entry->end,
            ];
        }
    }

    public function jsonSerialize()
    {
        return array_values(iterator_to_array($this->jsonData()));
    }

    public function getDuration(): ?int
    {
        if (count($this) === 0) {
            return null;
        }

        $min = $this->entries[0]->start;
        $max = max(array_column($this->entries, 'end'));

        return round(($max - $min) / 1000); // Seconds
    }

    public function addEntry($uri): TimelineEntry
    {
        return $this->entries[] = new TimelineEntry($uri);
    }

    public function count()
    {
        return count($this->entries);
    }

    public function dump(): void
    {
        $template = file_get_contents(static::TEMPLATE);

        $data = [
            '/* GUZZLE_TIMELINE_JSON */' => json_encode($this, JSON_PRETTY_PRINT),
            '/* GUZZLE_TIMELINE_WIDTH */' => $this->getDuration() * 10,
        ];

        $report = strtr($template, $data);

        file_put_contents($this->fileName, $report);
    }

    public function __destruct()
    {
        $this->dump();
    }
}
