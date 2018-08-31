<?php

namespace Alexa\Dispatcher;

use Alexa\Dispatcher\Exception\ExceptionMapper;
use Alexa\Dispatcher\Request\Handlers\HandlerAdapter;
use Alexa\Dispatcher\Request\Handlers\HandlerInput;
use Alexa\Dispatcher\Request\Interceptors\RequestInterceptor;
use Alexa\Dispatcher\Request\Interceptors\ResponseInterceptor;
use Alexa\Dispatcher\Request\Mappers\RequestMapper;
use Alexa\Exceptions\UnhandledSkillException;
use Alexa\Model\Response;
use Psalm\Issue\MixedAssignment;
use Throwable;

/**
 * Using a list of {@link RequestMapper} this class tries to find
 * a handler for a request, then delegates the invocation to a {@link HandlerAdapter} that
 * supports such a handler.
 */
class DefaultRequestDispatcher implements RequestDispatcher
{
    /** @var HandlerAdapter[] */
    protected $handlerAdapters = [];

    /** @var RequestMapper[] */
    protected $requestMappers = [];

    /** @var ExceptionMapper|null */
    protected $exceptionMapper = null;

    /** @var RequestInterceptor[] */
    protected $requestInterceptors = [];

    /** @var ResponseInterceptor[] */
    protected $responseInterceptors = [];

    private function __construct()
    {
    }

    /**
     * @param HandlerInput $input
     * @return Response|null
     */
    public function dispatch(HandlerInput $input)
    {
        try {
            return $this->doDispatch($input);
        } catch (Throwable $e) {
            $exceptionHandler = null;
            if ($this->exceptionMapper !== null) {
                $exceptionHandler = $this->exceptionMapper->getHandler($input, $e);
            }
            if ($exceptionHandler !== null) {
                return $exceptionHandler->handle($input, $e);
            } else {
                throw new UnhandledSkillException('No suitable exception handler found', 0, $e);
            }
        }
    }

    /**
     * @param HandlerInput $input
     * @return Response|null
     */
    private function doDispatch(HandlerInput $input)
    {
        foreach ($this->requestInterceptors as $requestInterceptor) {
            $requestInterceptor->process($input);
        }

        $handlerChain = null;
        foreach ($this->requestMappers as $mapper) {
            $handlerChain = $mapper->requestHandlerChain($input);
            if ($handlerChain) {
                break;
            }
        }

        if ($handlerChain === null) {
            throw new \RuntimeException('Unable to find a suitable request handler');
        }

        /** @var MixedAssignment $requestHandler */
        $requestHandler = $handlerChain->requestHandler();

        $handlerAdapter = null;
        foreach ($this->handlerAdapters as $adapter) {
            if ($adapter->supports($requestHandler)) {
                $handlerAdapter = $adapter;
                break;
            }
        }

        if ($handlerAdapter == null) {
            throw new \RuntimeException('Unable to find a suitable handler adapter');
        }

        foreach ($handlerChain->requestInterceptors() as $requestInterceptor) {
            $requestInterceptor->process($input);
        }

        $response = $handlerAdapter->execute($input, $requestHandler);

        foreach ($handlerChain->responseInterceptors() as $responseInterceptor) {
            $responseInterceptor->process($input, $response);
        }
        foreach ($this->responseInterceptors as $responseInterceptor) {
            $responseInterceptor->process($input, $response);
        }

        return $response;
    }

    public static function builder(): DefaultRequestDispatcherBuilder
    {
        $instance = new self;
        $constructor =
            /**
             * @param HandlerAdapter[] $handlerAdapters
             * @param RequestMapper[] $requestMappers
             * @param ExceptionMapper|null $exceptionMapper
             * @param RequestInterceptor[] $requestInterceptors
             * @param ResponseInterceptor[] $responseInterceptors
             * @return DefaultRequestDispatcher
             */
            function (
                array $handlerAdapters,
                array $requestMappers,
                $exceptionMapper,
                array $requestInterceptors,
                array $responseInterceptors
            ) use ($instance) {
                $instance->handlerAdapters = $handlerAdapters;
                $instance->requestMappers = $requestMappers;
                $instance->exceptionMapper = $exceptionMapper;
                $instance->requestInterceptors = $requestInterceptors;
                $instance->responseInterceptors = $responseInterceptors;
                return $instance;
            };
        return new class($constructor) extends DefaultRequestDispatcherBuilder
        {
            public function __construct(callable $constructor)
            {
                parent::__construct($constructor);
            }
        };
    }
}
