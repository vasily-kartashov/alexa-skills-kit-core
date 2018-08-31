<?php

namespace Alexa\Dispatcher\Request\Mappers;

use Alexa\Dispatcher\Request\Handlers\HandlerInput;
use Alexa\Dispatcher\Request\Handlers\RequestHandlerChain;

/**
 * Routes the request to appropriate controller.
 */
interface RequestMapper
{
    /**
     * Routes the request to appropriate controller and retrieves request handler chains
     *
     * @param HandlerInput $input
     * @return RequestHandlerChain|null
     */
    public function requestHandlerChain(HandlerInput $input);
}
