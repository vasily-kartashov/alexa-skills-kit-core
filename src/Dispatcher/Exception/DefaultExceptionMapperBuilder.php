<?php

namespace Alexa\Dispatcher\Exception;

abstract class DefaultExceptionMapperBuilder
{
    /**
     * @var callable
     * @psalm-var callable(array<ExceptionHandler>):DefaultExceptionMapper
     */
    protected $constructor;

    /** @var ExceptionHandler[] */
    private $exceptionHandlers = [];

    /**
     * @param callable $constructor
     * @psalm-param callable(array<ExceptionHandler>):DefaultExceptionMapper $constructor
     */
    protected function __construct(callable $constructor)
    {
        $this->constructor = $constructor;
    }

    /**
     * List of exception handlers to use in the handler chain. Handlers will accessed in the order
     * determined by the list.
     *
     * @param ExceptionHandler[] $handlers
     * @return DefaultExceptionMapperBuilder
     */
    public function withExceptionHandlers(ExceptionHandler ...$handlers): self
    {
        foreach ($handlers as $handler) {
            $this->exceptionHandlers[] = $handler;
        }
        return $this;
    }

    public function build(): DefaultExceptionMapper
    {
        return ($this->constructor)($this->exceptionHandlers);
    }
}
