<?php

namespace Alexa\Dispatcher\Request\Handlers;

use Alexa\Attributes\AttributesManager;
use Alexa\Model\RequestEnvelope;
use Alexa\Model\Response;
use Alexa\Model\ResponseBuilder;

/**
 * Input to request handler.
 */
class HandlerInput
{
    /** @var RequestEnvelope */
    protected $requestEnvelope;

    /** @var AttributesManager|null */
    protected $attributesManager;

    /** @var mixed */
    protected $context;

    /** @var ResponseBuilder */
    protected $responseBuilder;

    private function __construct()
    {
    }

    public function requestEnvelope(): RequestEnvelope
    {
        return $this->requestEnvelope;
    }

    /**
     * @return AttributesManager|null
     */
    public function attributesManager()
    {
        return $this->attributesManager;
    }

    /**
     * @return mixed
     */
    public function context()
    {
        return $this->context;
    }

    public function responseBuilder(): ResponseBuilder
    {
        return $this->responseBuilder;
    }

    public static function builder(): HandlerInputBuilder
    {
        $instance = new self;
        $constructor =
            /**
             * @param RequestEnvelope $requestEnvelope
             * @param AttributesManager|null $attributesManager
             * @param mixed $context
             * @return HandlerInput
             */
            function (RequestEnvelope $requestEnvelope, $attributesManager, $context) use ($instance): HandlerInput {
                $instance->requestEnvelope = $requestEnvelope;
                $instance->attributesManager = $attributesManager;
                $instance->context = $context;
                $instance->responseBuilder = Response::builder();
                return $instance;
            };

        return new class($constructor) extends HandlerInputBuilder
        {
            public function __construct(callable $constructor)
            {
                parent::__construct($constructor);
            }
        };
    }
}
