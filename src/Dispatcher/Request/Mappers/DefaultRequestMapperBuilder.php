<?php

namespace Alexa\Dispatcher\Request\Mappers;

use Alexa\Dispatcher\Request\Handlers\DefaultRequestHandlerChain;
use Alexa\Dispatcher\Request\Handlers\RequestHandlerChain;

abstract class DefaultRequestMapperBuilder
{
    /**
     * @var callable
     * @psalm-var callable(array<RequestHandlerChain>):DefaultRequestMapper
     */
    private $constructor;

    /** @var RequestHandlerChain[] */
    private $handlerChains = [];

    protected function __construct(callable $constructor)
    {
        $this->constructor = $constructor;
    }

    public function withHandlerChains(RequestHandlerChain ...$handlerChains): self
    {
        foreach ($handlerChains as $handlerChain) {
            $this->handlerChains[] = $handlerChain;
        }
        return $this;
    }

    public function build(): DefaultRequestMapper
    {
        return ($this->constructor)($this->handlerChains);
    }
}
