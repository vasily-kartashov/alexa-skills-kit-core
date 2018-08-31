<?php

namespace Alexa\Attributes\Persistence;

use Alexa\Exceptions\PersistenceException;
use Alexa\Model\RequestEnvelope;

/**
 * Persistence adapter is responsible for storing and retrieving attributes from a persistence layer.
 */
interface PersistenceAdapter
{
    /**
     * @param RequestEnvelope $envelope
     * @return array<string,mixed>
     * @throws PersistenceException
     */
    public function attributes(RequestEnvelope $envelope): array;

    /**
     * @param RequestEnvelope $envelope
     * @param array<string,mixed> $attributes
     * @return void
     * @throws PersistenceException
     */
    public function saveAttributes(RequestEnvelope $envelope, array $attributes);
}
