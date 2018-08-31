<?php

namespace Alexa\Dispatcher;

use Alexa\Dispatcher\Exception\ExceptionMapper;
use Alexa\Dispatcher\Request\Handlers\HandlerAdapter;
use Alexa\Dispatcher\Request\Interceptors\RequestInterceptor;
use Alexa\Dispatcher\Request\Interceptors\ResponseInterceptor;
use Alexa\Dispatcher\Request\Mappers\RequestMapper;

abstract class DefaultRequestDispatcherBuilder
{
    /**
     * @var callable
     * @psalm-var callable(array<HandlerAdapter>,array<RequestMapper>,ExceptionMapper|null,array<RequestInterceptor>,array<ResponseInterceptor>):DefaultRequestDispatcher
     */
    private $constructor;

    /** @var HandlerAdapter[] */
    private $handlerAdapters = [];

    /** @var RequestMapper[] */
    private $requestMappers = [];

    /** @var ExceptionMapper|null */
    private $exceptionMapper;

    /** @var RequestInterceptor[] */
    private $requestInterceptors;

    /** @var ResponseInterceptor[] */
    private $responseInterceptors;

    /**
     * @param callable $constructor
     * @psalm-param @psalm-var callable(array<HandlerAdapter>,array<RequestMapper>,ExceptionMapper|null,array<RequestInterceptor>,array<ResponseInterceptor>):DefaultRequestDispatcher $constructor
     */
    protected function __construct(callable $constructor)
    {
        $this->constructor = $constructor;
    }

    public function withHandlerAdapters(HandlerAdapter ...$adapters): self
    {
        foreach ($adapters as $adapter) {
            $this->handlerAdapters[] = $adapter;
        }
        return $this;
    }

    public function withRequestMappers(RequestMapper ...$mappers): self
    {
        foreach ($mappers as $mapper) {
            $this->requestMappers[] = $mapper;
        }
        return $this;
    }

    public function withExceptionMapper(ExceptionMapper $mapper): self
    {
        $this->exceptionMapper = $mapper;
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

    public function build(): DefaultRequestDispatcher
    {
        return ($this->constructor)(
            $this->handlerAdapters,
            $this->requestMappers,
            $this->exceptionMapper,
            $this->requestInterceptors,
            $this->responseInterceptors
        );
    }
}
