<?php

namespace Alexa\Dispatcher\Request\Handlers;

use Alexa\Dispatcher\Request\Interceptors\RequestInterceptor;
use Alexa\Dispatcher\Request\Interceptors\ResponseInterceptor;

abstract class GenericRequestHandlerChainBuilder
{
    /**
     * @var callable
     * @psalm-var callable(mixed,array<RequestInterceptor>,array<ResponseInterceptor>):GenericRequestHandlerChain
     */
    private $constructor;

    /** @var mixed */
    private $handler = null;

    /** @var RequestInterceptor[] */
    private $requestInterceptors = [];

    /** @var ResponseInterceptor[] */
    private $responseInterceptors = [];


    protected function __construct(callable $constructor)
    {
        $this->constructor = $constructor;
    }

    /**
     * @param mixed $handler
     * @return self
     */
    public function withRequestHandler($handler): self
    {
        $this->handler = $handler;
        return $this;
    }

    public function withRequestInterceptors(RequestInterceptor ...$interceptors): self
    {
        foreach ($interceptors as $interceptor) {
            $this->requestInterceptors[] = $interceptor;
        }
        return $this;
    }

    public function withResponseInterceptors(ResponseInterceptor ...$interceptors): self
    {
        foreach ($interceptors as $interceptor) {
            $this->responseInterceptors[] = $interceptor;
        }
        return $this;
    }

    public function build(): GenericRequestHandlerChain
    {
        return ($this->constructor)($this->handler, $this->requestInterceptors, $this->responseInterceptors);
    }
}
