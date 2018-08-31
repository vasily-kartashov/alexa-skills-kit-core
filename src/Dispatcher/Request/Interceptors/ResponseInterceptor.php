<?php

namespace Alexa\Dispatcher\Request\Interceptors;

use Alexa\Dispatcher\Request\Handlers\HandlerInput;
use Alexa\Model\Response;

/**
 * Response Interceptor contains the logic to be executed after handler returns.
 */
interface ResponseInterceptor
{
    /**
     * @param HandlerInput $input
     * @param Response|null $response
     * @return void
     */
    public function process(HandlerInput $input, $response);
}
