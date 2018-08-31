<?php

namespace Alexa\Dispatcher;

use Alexa\Dispatcher\Request\Handlers\HandlerInput;
use Alexa\Exceptions\SkillsKitException;
use Alexa\Model\Response;

/**
 * Receives a request, dispatches to the customer's code and returns the output
 */
interface RequestDispatcher
{
    /**
     * Dispatches an incoming request to the appropriate request handler and returns the output
     *
     * @param HandlerInput $input
     * @return Response|null
     * @throws SkillsKitException
     */
    public function dispatch(HandlerInput $input);
}
