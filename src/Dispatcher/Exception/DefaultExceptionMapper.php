<?php

namespace Alexa\Dispatcher\Exception;

use Alexa\Dispatcher\Request\Handlers\HandlerInput;
use Throwable;

class DefaultExceptionMapper implements ExceptionMapper
{
    /** @var ExceptionHandler[] */
    private $exceptionHandlers = [];

    private function __construct()
    {
    }

    /**
     * @param HandlerInput $input
     * @param Throwable $throwable
     * @return ExceptionHandler|null
     */
    public function getHandler(HandlerInput $input, Throwable $throwable)
    {
        foreach ($this->exceptionHandlers as $exceptionHandler) {
            if ($exceptionHandler->canHandle($input, $throwable)) {
                return $exceptionHandler;
            }
        }
        return null;
    }

    public static function builder(): DefaultExceptionMapperBuilder
    {
        $instance = new self;
        $constructor =
            /**
             * @param ExceptionHandler[] $exceptionHandlers
             * @return DefaultExceptionMapper
             */
            function (array $exceptionHandlers) use ($instance): DefaultExceptionMapper {
                $instance->exceptionHandlers = $exceptionHandlers;
                return $instance;
            };

        return new class($constructor) extends DefaultExceptionMapperBuilder
        {
            public function __construct(callable $constructor)
            {
                parent::__construct($constructor);
            }
        };
    }
}
