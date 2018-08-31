<?php

namespace Alexa\Attributes;

use Alexa\Attributes\Persistence\PersistenceAdapter;
use Alexa\Model\RequestEnvelope;

/**
 * Provider for attributes that can be stored on three levels: request, session and persistence.
 */
class AttributesManager
{
    /** @var PersistenceAdapter|null */
    private $persistenceAdapter = null;

    /** @var RequestEnvelope */
    private $requestEnvelope;

    /** @var array<string,mixed> */
    protected $sessionAttributes = [];

    /** @var array<string,mixed> */
    protected $persistentAttributes = [];

    /** @var array<string,mixed> */
    protected $requestAttributes = [];

    /** @var bool */
    protected $persistenceAttributesSet = false;

    private function __construct()
    {
    }

    /**
     * @return array<string,mixed>
     */
    public function sessionAttributes(): array
    {
        if ($this->requestEnvelope->session() === null) {
            throw new \RuntimeException('Attempting to read session attributes from out of session request');
        }
        return $this->sessionAttributes;
    }

    /**
     * @param array<string,mixed> $attributes
     * @return AttributesManager
     */
    public function withSessionAttributes(array $attributes): self
    {
        if ($this->requestEnvelope->session() === null) {
            throw new \RuntimeException('Attempting to read session attributes from out of session request');
        }
        $this->sessionAttributes = $attributes;
        return $this;
    }

    /**
     * @return array<string,mixed>
     */
    public function persistentAttributes(): array
    {
        if ($this->persistenceAdapter == null) {
            throw new \RuntimeException('Attempting to read persistence attributes without configured persistence adapter');
        }
        if (!$this->persistenceAttributesSet) {
            $this->persistentAttributes = $this->persistenceAdapter->attributes($this->requestEnvelope);
            $this->persistenceAttributesSet = true;
        }
        return $this->persistentAttributes;
    }

    /**
     * @param array<string,mixed> $attributes
     * @return AttributesManager
     */
    public function withPersistentAttributes(array $attributes): self
    {
        if ($this->persistenceAdapter == null) {
            throw new \RuntimeException('Attempting to set persistence attributes without configured persistence adapter');
        }
        $this->persistentAttributes = $attributes;
        $this->persistenceAttributesSet = true;
        return $this;
    }

    /**
     * @return array<string,mixed>
     */
    public function requestAttributes(): array
    {
        return $this->requestAttributes;
    }

    /**
     * @param array<string,mixed> $attributes
     * @return AttributesManager
     */
    public function withRequestAttributes(array $attributes): self
    {
        $this->requestAttributes = $attributes;
        return $this;
    }

    /**
     * @return void
     */
    public function savePersistentAttributes()
    {
        if ($this->persistenceAdapter == null) {
            throw new \RuntimeException('Attempting to save persistence attributes without configured persistence adapter');
        }
        if ($this->persistenceAttributesSet) {
            $this->persistenceAdapter->saveAttributes($this->requestEnvelope, $this->persistentAttributes);
        }
    }

    public static function builder(): AttributesManagerBuilder
    {
        $instance = new self;
        $constructor =
            /**
             * @param RequestEnvelope $requestEnvelope
             * @param PersistenceAdapter|null $persistenceAdapter
             * @return AttributesManager
             */
            function (RequestEnvelope $requestEnvelope, $persistenceAdapter) use ($instance) {
                $instance->persistenceAdapter = $persistenceAdapter;
                $instance->requestEnvelope = $requestEnvelope;
                $session = $requestEnvelope->session();
                if ($session !== null) {
                    /** @var array<string,mixed> */
                    $attributes = $session->attributes();
                    if ($attributes) {
                        $instance->sessionAttributes = $attributes;
                    }
                }
                return $instance;
            };

        return new class($constructor) extends AttributesManagerBuilder
        {
            public function __construct(callable $constructor)
            {
                parent::__construct($constructor);
            }
        };
    }
}
