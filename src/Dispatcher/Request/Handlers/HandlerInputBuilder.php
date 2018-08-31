<?php

namespace Alexa\Dispatcher\Request\Handlers;

use Alexa\Attributes\Persistence\PersistenceAdapter;
use Alexa\Model\RequestEnvelope;

abstract class HandlerInputBuilder
{
    /**
     * @var callable
     * @psalm-var callable(RequestEnvelope|null,PersistenceAdapter|null,mixed):HandlerInput
     */
    private $constructor;

    /** @var RequestEnvelope|null */
    private $requestEnvelope;

    /** @var PersistenceAdapter|null */
    private $persistenceAdapter;

    /** @var mixed */
    private $context;

    /**
     * @param callable $constructor
     * @psalm-param callable(RequestEnvelope|null,PersistenceAdapter|null,mixed):HandlerInput $constructor
     */
    protected function __construct(callable $constructor)
    {
        $this->constructor = $constructor;
    }

    public function withRequestEnvelope(RequestEnvelope $requestEnvelope): self
    {
        $this->requestEnvelope = $requestEnvelope;
        return $this;
    }

    public function withPersistenceAdapter(PersistenceAdapter $persistenceAdapter): self
    {
        $this->persistenceAdapter = $persistenceAdapter;
        return $this;
    }

    /**
     * @param mixed $context
     * @return HandlerInputBuilder
     */
    public function withContext($context): self
    {
        $this->context = $context;
        return $this;
    }

    public function build(): HandlerInput
    {
        return ($this->constructor)($this->requestEnvelope, $this->persistenceAdapter, $this->context);
    }
}
