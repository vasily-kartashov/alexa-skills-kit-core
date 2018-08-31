<?php

namespace Alexa;

use Alexa\Attributes\Persistence\PersistenceAdapter;
use Alexa\Dispatcher\DefaultRequestDispatcher;
use Alexa\Dispatcher\Exception\DefaultExceptionMapper;
use Alexa\Dispatcher\Exception\ExceptionHandler;
use Alexa\Dispatcher\Request\Handlers\DefaultHandlerAdapter;
use Alexa\Dispatcher\Request\Handlers\DefaultRequestHandlerChain;
use Alexa\Dispatcher\Request\Handlers\RequestHandler;
use Alexa\Dispatcher\Request\Interceptors\RequestInterceptor;
use Alexa\Dispatcher\Request\Interceptors\ResponseInterceptor;
use Alexa\Dispatcher\Request\Mappers\DefaultRequestMapper;
use Alexa\Dispatcher\RequestDispatcher;

abstract class SkillBuilder
{
    /**
     * @var callable
     * @psalm-var callable(RequestDispatcher,PersistenceAdapter|null,string|null):Skill
     */
    private $constructor;

    /** @var RequestHandler[] */
    protected $requestHandlers;

    /** @var ExceptionHandler[] */
    protected $exceptionHandlers;

    /** @var RequestInterceptor[]  */
    protected $requestInterceptors;

    /** @var ResponseInterceptor[]  */
    protected $responseInterceptors;

    /** @var PersistenceAdapter|null */
    protected $persistenceAdapter;

    /** @var string|null */
    protected $skillId;

    /**
     * @param callable $constructor
     * @psalm-param callable(RequestDispatcher,PersistenceAdapter|null,string|null):Skill $constructor
     */
    protected function __construct(callable $constructor)
    {
        $this->constructor = $constructor;
    }

    public function withRequestHandlers(RequestHandler ...$handlers): self
    {
        foreach ($handlers as $handler) {
            $this->requestHandlers[] = $handler;
        }
        return $this;
    }

    public function withRequestInterceptors(RequestInterceptor ...$interceptors): self
    {
        foreach ($interceptors as $interceptor) {
            $this->requestInterceptors[] = $interceptor;
        }
        return $this;
    }

    public function withResponseInterceptors(ResponseInterceptor ...$interceptors): self
    {
        foreach ($interceptors as $interceptor) {
            $this->responseInterceptors[] = $interceptor;
        }
        return $this;
    }

    public function withExceptionHandlers(ExceptionHandler ...$handlers): self
    {
        foreach ($handlers as $handler) {
            $this->exceptionHandlers[] = $handler;
        }
        return $this;
    }

    public function withPersistenceAdapter(PersistenceAdapter $persistenceAdapter): self
    {
        $this->persistenceAdapter = $persistenceAdapter;
        return $this;
    }

    public function withSkillId(String $skillId): self
    {
        $this->skillId = $skillId;
        return $this;
    }

    public function build(): Skill
    {
        $requestDispatcherBuilder = DefaultRequestDispatcher::builder();

        $handlerChains = [];
        foreach ($this->requestHandlers as $handler) {
            $handlerChains[] = DefaultRequestHandlerChain::builder()
                ->withRequestHandler($handler)
                ->build();
        }

        $requestDispatcherBuilder
            ->withExceptionMapper(
                DefaultExceptionMapper::builder()
                    ->withExceptionHandlers(...$this->exceptionHandlers)
                    ->build()
            )
            ->withRequestMappers(
                DefaultRequestMapper::builder()
                    ->withHandlerChains(...$handlerChains)
                    ->build()
            )
            ->withHandlerAdapters(new DefaultHandlerAdapter())
            ->withRequestInterceptors(...$this->requestInterceptors)
            ->withResponseInterceptors(...$this->responseInterceptors);

        /** @var RequestDispatcher $requestDispatcher */
        $requestDispatcher = $requestDispatcherBuilder->build();

        return ($this->constructor)(
            $requestDispatcher,
            $this->persistenceAdapter,
            $this->skillId
        );
    }
}
