<?php

namespace Alexa\Dispatcher\Request\Handlers;

class DefaultHandlerAdapter implements HandlerAdapter
{
    public function supports($handler): bool
    {
        return $handler instanceof RequestHandler;
    }

    public function execute(HandlerInput $input, $handler)
    {
        /** @var RequestHandler $handler */
        return $handler->handle($input);
    }
}
