<?php

namespace Alexa\Dispatcher\Exception;

use Alexa\Dispatcher\Request\Handlers\HandlerInput;
use Alexa\Model\Response;
use Throwable;

/**
 * Handles ones or more exception types and optionally produces a response.
 */
interface ExceptionHandler
{
    /**
     * Returns true if the implementation can handle the specified throwable
     *
     * @param HandlerInput $input handler input
     * @param Throwable $throwable exception
     * @return bool
     */
    public function canHandle(HandlerInput $input, Throwable $throwable): bool;

    /**
     * Handles the exception
     *
     * @param HandlerInput $input handler input
     * @param Throwable $throwable exception
     * @return Response|null
     */
    public function handle(HandlerInput $input, Throwable $throwable);
}
