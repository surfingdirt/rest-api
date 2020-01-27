<?php
use Zipkin\Annotation;
use Zipkin\Endpoint;
use Zipkin\Propagation\RequestHeaders;
use Zipkin\Propagation\TraceContext;
use Zipkin\Reporters\Http;
use Zipkin\Samplers\BinarySampler;
use Zipkin\TracingBuilder;

use Nyholm\Psr7\Request;

class Tracer
{
  protected $_tracing;

  protected $_tracer;

  public function __construct() {
    $endpoint = Endpoint::create(OPENTRACING_SERVICE_NAME, '0.0.0.0', null, OPENTRACING_ENDPOINT_PORT);
    $reporter = new Http(
      null,
      array('endpoint_url' => 'http://'.OPENTRACING_ENDPOINT_HOST.':'.OPENTRACING_ENDPOINT_PORT.'/api/v2/spans')
    );
    $sampler = BinarySampler::createAsAlwaysSample();
    $this->_tracing = TracingBuilder::create()
      ->havingLocalEndpoint($endpoint)
      ->havingSampler($sampler)
      ->havingReporter($reporter)
      ->build();

    $this->_instance = $this->_tracing->getTracer();
  }

  private function _getRequest() {
    $currentUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") .
      "://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
    $request = new Request($_SERVER['REQUEST_METHOD'], $currentUrl, getallheaders());
    return $request;
  }

  private function _logParts($logMsg) {
    if (DEBUG) {
      Globals::getLogger()->tracing(implode("\n\t", $logMsg));
    }
  }

  public function startTrace() {
    $logMsg = ['Start trace for '.$_SERVER['REQUEST_URI']];

    $extractor = $this->_tracing->getPropagation()->getExtractor(new RequestHeaders);
    $extracted = $extractor($this->_getRequest());

    if ($extracted instanceof TraceContext) {
      $traceId = $extracted->getTraceId();
      $logMsg[] = "Existing trace - id: $traceId";
      if ($traceId) {
        $this->_logParts($logMsg);
        return $this->_instance->newChild($extracted);
      }
    }

    $samplingFlags = null;
    $newSpan = $this->_instance->newTrace($samplingFlags);
    $traceId = $newSpan->getContext()->getTraceId();
    $logMsg[] = "New trace - id: $traceId";

    $this->_logParts($logMsg);
    return $newSpan;
  }

  public function flush() {
    return $this->_instance->flush();
  }
}