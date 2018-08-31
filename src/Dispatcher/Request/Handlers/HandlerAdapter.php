<?php

namespace Alexa\Dispatcher\Request\Handlers;

use Alexa\Model\Response;

/**
 * Abstracts the handling of handling a request for specific types of handlers.
 */
interface HandlerAdapter
{
    /**
     * Returns true if the adapter supports the type of handler. Usually by type.
     *
     * @param mixed $handler request handler
     * @return bool true if the adapter supports the type of handler
     */
    public function supports($handler): bool;

    /**
     * Executes the request handler with the supplied input.
     *
     * @param HandlerInput $input input containing request envelope, handler context and forwarding target
     * @param mixed $handler request handler
     * @return Response|null result of executing the request handler
     */
    public function execute(HandlerInput $input, $handler);
}
