# Guzzle Timeline Reports

A simple helper for profiling async requests.

## Basic timeline report

This report shows all the requests on a global timeline ([example](docs/report_timeline.html)).

## Execution flow reports

If you want to profile Guzzle's concurrency feature, then the execution flow report can help you a lot. It arranges all 
the requests by virtual "threads", so you are able to see (for example) how good the resource utilization is. [An example 
report](docs/report_ex_flow.html).

## In your code

### Basic usage

Just add appropriate middleware to your stack:

```php
$handler = \GuzzleHttp\HandlerStack::create();
// For basic timeline report
$handler->push(TimelineReporter::middleware(__DIR__ . '/timeline.html'), 'timeline_report');
// For execution flow report
$handler->push(ExecutionFlowTimelineReporter::middleware(__DIR__ . '/ex_flow.html'), 'ex_flow_report');

$client = new \GuzzleHttp\Client([
    'handler' => $handler,
]);
```

### Multiple reports

If you profile a long living process with a lot of HTTP requests, it's possible to split the report into chunks. Just 
use `${NUMBER}` placeholder in your report file name template and pass the max number of entries per report chunk.

```php
$handler = \GuzzleHttp\HandlerStack::create();
$handler->push(TimelineReporter::middleware(__DIR__ . '/timeline_${NUMBER}.html', null, 250), 'timeline_report');

$client = new \GuzzleHttp\Client([
    'handler' => $handler,
]);
```
