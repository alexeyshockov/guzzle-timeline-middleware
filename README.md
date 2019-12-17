# Guzzle Timeline Reports

A simple helper for profiling async requests.

## Basic timeline report

This report shows all the requests on a global timeline ([example](http://alexey.shockov.com/guzzle-timeline-middleware/report_timeline.html)).

## Execution flow reports

If you want to profile Guzzle's concurrency feature, then the execution flow report can help you a lot. It arranges all 
the requests by virtual "threads", so you are able to see (for example) how good the resource utilization is. [An example 
report](http://alexey.shockov.com/guzzle-timeline-middleware/report_ex_flow.html).

## In your code

Install as usual, with Composer: `composer require --dev alexeyshockov/guzzle-timeline-middleware`

### Basic usage

Just add appropriate middleware to your stack:

```php
$handler = HandlerStack::create();
// For basic timeline report
$handler->push(TimelineReporter::middleware(__DIR__ . '/timeline.html'), 'timeline_report');
// For execution flow report
$handler->push(ExecutionFlowTimelineReporter::middleware(__DIR__ . '/ex_flow.html'), 'ex_flow_report');

$client = new \GuzzleHttp\Client([
    'handler' => $handler,
]);
```

### Splitting a huge report into chunks

If you profile a long living process with a lot of HTTP requests, it's possible to split the report into chunks. Just 
use `${NUMBER}` placeholder in your report file name template and pass the max number of entries per report chunk.

```php
$handler = HandlerStack::create();
$handler->push(TimelineReporter::middleware(__DIR__ . '/timeline_${NUMBER}.html', null, 250), 'timeline_report');

$client = new \GuzzleHttp\Client([
    'handler' => $handler,
]);
```
