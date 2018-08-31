<?php

namespace Alexa\Dispatcher\Request\Interceptors;

use Alexa\Dispatcher\Request\Handlers\HandlerInput;

/**
 * Request Interceptor contains the logic to execute before handler is called.
 */
interface RequestInterceptor
{
    public function process(HandlerInput $input);
}
