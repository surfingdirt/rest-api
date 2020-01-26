<?php
use Zipkin\Annotation;
use Zipkin\Endpoint;
use Zipkin\Propagation\RequestHeaders;
use Zipkin\Reporters\Http;
use Zipkin\Samplers\BinarySampler;
use Zipkin\TracingBuilder;

use Nyholm\Psr7\Request;

class Tracer
{
  protected $_instance;

  protected $_extracted;

  public function __construct() {
    $endpoint = Endpoint::create(OPENTRACING_SERVICE_NAME, '0.0.0.0', null, OPENTRACING_ENDPOINT_PORT);
    $reporter = new Http(
      null,
      array('endpoint_url' => 'http://'.OPENTRACING_ENDPOINT_HOST.':'.OPENTRACING_ENDPOINT_PORT.'/api/v2/spans')
    );
    $sampler = BinarySampler::createAsAlwaysSample();
    $tracing = TracingBuilder::create()
      ->havingLocalEndpoint($endpoint)
      ->havingSampler($sampler)
      ->havingReporter($reporter)
      ->build();

    // TODO: move this to a new method (doTrace?) which will decide whether to call newTrace or newChild
    $extractor = $tracing->getPropagation()->getExtractor(new RequestHeaders);
    $this->_extracted = $extractor($this->_getRequest());

    $this->_instance = $tracing->getTracer();
  }

  private function _getRequest() {
    $currentUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") .
      "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    $request = new Request($_SERVER['REQUEST_METHOD'], $currentUrl, getallheaders());
    return $request;
  }

  public function newTrace() {
    $samplingFlags = null;
    return $this->_instance->newTrace($samplingFlags);
  }

  public function newChild() {
    return $this->_instance->newChild($this->_extracted);
  }

  public function flush() {
    return $this->_instance->flush();
  }
}