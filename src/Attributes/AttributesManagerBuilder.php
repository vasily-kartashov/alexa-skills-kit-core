<?php

namespace Alexa\Attributes;

use Alexa\Attributes\Persistence\PersistenceAdapter;
use Alexa\Model\RequestEnvelope;

abstract class AttributesManagerBuilder
{
    /**
     * @var callable
     * @psalm-var callable(RequestEnvelope,PersistenceAdapter|null):AttributesManager
     */
    private $constructor;

    /** @var PersistenceAdapter|null */
    private $persistenceAdapter;

    /** @var RequestEnvelope|null */
    private $requestEnvelope;

    public function __construct(callable $constructor)
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

    public function build(): AttributesManager
    {
        if ($this->requestEnvelope === null) {
            throw new \RuntimeException('Cannot initialize attributes manager without request envelop provided');
        }
        return ($this->constructor)($this->requestEnvelope, $this->persistenceAdapter);
    }
}
