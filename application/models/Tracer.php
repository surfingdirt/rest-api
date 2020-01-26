<?php
use Zipkin\Annotation;
use Zipkin\Endpoint;
use Zipkin\Samplers\BinarySampler;
use Zipkin\TracingBuilder;
use Zipkin\Reporters\Http;

class Tracer
{
  protected $_instance;

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
    $this->_instance = $tracing->getTracer();
  }

  public function newTrace() {
    return $this->_instance->newTrace();
  }

  public function newChild() {
    return $this->_instance->newChild();
  }

  public function flush() {
    return $this->_instance->flush();
  }
}