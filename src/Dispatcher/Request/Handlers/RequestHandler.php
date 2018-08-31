<?php

namespace Alexa\Dispatcher\Request\Handlers;

use Alexa\Model\Response;

/**
 * Request handlers are responsible for taking a request and generating a response
 */
interface RequestHandler
{
    /**
     * Returns true if the handler can dispatch the current request
     *
     * @param HandlerInput $input request envelope containing request, context and state
     * @return bool true if the handler can dispatch the current request
     */
    public function canHandle(HandlerInput $input): bool;

    /**
     * Accepts an input and generates a response
     *
     * @param HandlerInput $input request envelope containing request, context and state
     * @return Response|null an optional {@link Response} from the handler.
     */
    public function handle(HandlerInput $input);
}
