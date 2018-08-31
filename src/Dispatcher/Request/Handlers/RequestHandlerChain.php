<?php

namespace Alexa\Dispatcher\Request\Handlers;

use Alexa\Dispatcher\Request\Interceptors\RequestInterceptor;
use Alexa\Dispatcher\Request\Interceptors\ResponseInterceptor;

/**
 * An interface containing the request handler and corresponding request/response interceptors.
 */
interface RequestHandlerChain
{
    /**
     * Returns the request handler
     *
     * @return mixed
     */
    public function requestHandler();

    /**
     * @return RequestInterceptor[]
     */
    public function requestInterceptors(): array;

    /**
     * @return ResponseInterceptor[]
     */
    public function responseInterceptors(): array;
}
