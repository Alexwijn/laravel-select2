<?php

namespace Alexwijn\Select2\Exceptions;

/**
 * Alexwijn\Select2\Exceptions\InvalidEngine
 */
class EngineNotFound extends \Exception
{
    public function __construct($source)
    {
        parent::__construct('No available engine for ' . \get_class($source));
    }
}
