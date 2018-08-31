<?php

namespace Alexa;

use Alexa\Attributes\Persistence\PersistenceAdapter;
use Alexa\Dispatcher\Request\Handlers\HandlerInput;
use Alexa\Dispatcher\RequestDispatcher;
use Alexa\Exceptions\SkillsKitException;
use Alexa\Model\RequestEnvelope;
use Alexa\Model\ResponseEnvelope;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * Top level container for request dispatcher which provides multiple builders for easy configuration.
 */
class Skill implements LoggerAwareInterface
{
    /** @var RequestDispatcher */
    protected $requestDispatcher;

    /** @var PersistenceAdapter|null */
    protected $persistenceAdapter;

    /** @var string|null */
    protected $skillId;

    /** @var LoggerInterface */
    protected $logger;

    private function __construct()
    {
        $this->logger = new NullLogger();
    }

    public static function builder(): SkillBuilder
    {
        $instance = new self;
        $constructor =
            /**
             * @param RequestDispatcher $requestDispatcher
             * @param PersistenceAdapter|null $persistenceAdapter
             * @param string|null $skillId
             * @return Skill
             */
            function (RequestDispatcher $requestDispatcher, $persistenceAdapter, $skillId) use ($instance) {
                $instance->requestDispatcher = $requestDispatcher;
                $instance->persistenceAdapter = $persistenceAdapter;
                $instance->skillId = $skillId;
                return $instance;
            };
        return new class($constructor) extends SkillBuilder
        {
            public function __construct(callable $constructor)
            {
                parent::__construct($constructor);
            }
        };
    }

    /**
     * @param RequestEnvelope $requestEnvelope
     * @param mixed $context
     * @return ResponseEnvelope
     */
    public function invoke(RequestEnvelope $requestEnvelope, $context = null): ResponseEnvelope
    {
        if ($this->skillId !== null) {
            $requestContext = $requestEnvelope->context();
            if ($requestContext) {
                $system = $requestContext->system();
                if ($system) {
                    $application = $system->application();
                    if ($application) {
                        if ($application->applicationId() !== $this->skillId) {
                            throw new SkillsKitException('Skill ID verification failed.');
                        }
                    }
                }
            }
        }

        $handlerInputBuilder = HandlerInput::builder()->withRequestEnvelope($requestEnvelope);
        if ($this->persistenceAdapter !== null) {
            $handlerInputBuilder->withPersistenceAdapter($this->persistenceAdapter);
        }
        if ($context !== null) {
            $handlerInputBuilder->withContext($context);
        }
        $handlerInput = $handlerInputBuilder->build();

        $response = $this->requestDispatcher->dispatch($handlerInput);

        $responseEnvelopeBuilder = ResponseEnvelope::builder()
            ->withVersion('')           // @todo fix
            ->withUserAgent('');      // @todo fix
        if ($response) {
            $responseEnvelopeBuilder->withResponse($response);
        }
        if ($requestEnvelope->session()) {
            $attributesManager = $handlerInput->attributesManager();
            if ($attributesManager) {
                $responseEnvelopeBuilder->withSessionAttributes($attributesManager->sessionAttributes());
            }
        }
        return $responseEnvelopeBuilder->build();
    }

    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }
}
