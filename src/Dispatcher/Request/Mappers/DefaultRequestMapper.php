<?php

namespace Alexa\Dispatcher\Request\Mappers;

use Alexa\Dispatcher\Request\Handlers\DefaultRequestHandlerChain;
use Alexa\Dispatcher\Request\Handlers\HandlerInput;
use Alexa\Dispatcher\Request\Handlers\RequestHandler;
use Alexa\Dispatcher\Request\Handlers\RequestHandlerChain;

class DefaultRequestMapper implements RequestMapper
{
    /** @var RequestHandlerChain[] */
    protected $handlerChains;

    private function __construct()
    {
    }

    /**
     * @param HandlerInput $input
     * @return RequestHandlerChain|null
     */
    public function requestHandlerChain(HandlerInput $input)
    {
        foreach ($this->handlerChains as $chain) {
            /** @psalm-suppress MixedAssignment */
            $handler = $chain->requestHandler();
            if ($handler instanceof RequestHandler) {
                if ($handler->canHandle($input)) {
                    return $chain;
                }
            }
        }
        return null;
    }

    public static function builder(): DefaultRequestMapperBuilder
    {
        $instance = new self;
        $constructor =
            /**
             * @param RequestHandlerChain[] $handlerChains
             * @return DefaultRequestMapper
             */
            function ($handlerChains) use ($instance): DefaultRequestMapper {
                $instance->handlerChains = $handlerChains;
                return $instance;
            };

        return new class($constructor) extends DefaultRequestMapperBuilder
        {
            public function __construct(callable $constructor)
            {
                parent::__construct($constructor);
            }
        };
    }
}
