<?php

namespace Alexa\Dispatcher\Exception;

use Alexa\Dispatcher\Request\Handlers\HandlerInput;
use Throwable;

/**
 * Used by the {@link DefaultRequestDispatcher} to act on unhandled exceptions. The exception mapper contains one or
 * more exception handlers. Handlers are accessed through the mapper to attempt to find a handler
 * that is compatible with the current exception.
 */
interface ExceptionMapper
{
    /**
     * Returns a suitable exception handler to dispatch the specified exception, if one exists.
     *
     * @param HandlerInput $input handler input
     * @param Throwable $throwable exception
     * @return ExceptionHandler|null optional exception handler if found
     */
    public function getHandler(HandlerInput $input, Throwable $throwable);
}
