<?php

namespace Alexa\Dispatcher\Request\Handlers;

use Alexa\Dispatcher\Request\Interceptors\RequestInterceptor;
use Alexa\Dispatcher\Request\Interceptors\ResponseInterceptor;

class GenericRequestHandlerChain implements RequestHandlerChain
{
    /** @var mixed */
    protected $handler = null;

    /** @var RequestInterceptor[] */
    protected $requestInterceptors = [];

    /** @var ResponseInterceptor[] */
    protected $responseInterceptors = [];

    protected function __construct()
    {
    }

    /**
     * @return mixed
     */
    public function requestHandler()
    {
        return $this->handler;
    }

    /**
     * @return RequestInterceptor[]
     */
    public function requestInterceptors(): array
    {
        return $this->requestInterceptors;
    }

    /**
     * @return ResponseInterceptor[]
     */
    public function responseInterceptors(): array
    {
        return $this->responseInterceptors;
    }

    public static function builder(): GenericRequestHandlerChainBuilder
    {
        $instance = new self;
        $constructor =
            /**
             * @param mixed $handler
             * @param RequestInterceptor[] $requestInterceptors
             * @param ResponseInterceptor[] $responseInterceptors
             * @return GenericRequestHandlerChain
             */
            function ($handler, array $requestInterceptors, array $responseInterceptors) use ($instance) {
                $instance->handler = $handler;
                $instance->requestInterceptors = $requestInterceptors;
                $instance->responseInterceptors = $responseInterceptors;
                return $instance;
            };

        return new class($constructor) extends GenericRequestHandlerChainBuilder
        {
            public function __construct(callable $constructor)
            {
                parent::__construct($constructor);
            }
        };
    }
}
