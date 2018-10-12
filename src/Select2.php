<?php

namespace Alexwijn\Select2;

use Alexwijn\Select2\Exceptions\EngineNotFound;
use Illuminate\Support\Traits\Macroable;

/**
 * Alexwijn\Select2\Select2
 */
class Select2
{
    use Macroable;

    /**
     * Make a engine instance from source.
     *
     * @param array $args
     * @return \Alexwijn\Select2\Contracts\Engine
     * @throws \Alexwijn\Select2\Exceptions\EngineNotFound
     */
    public static function make(...$args): Contracts\Engine
    {
        foreach (config('select2.engines') as $engine => $class) {
            if (is_a($class, Contracts\Engine::class, true) && $class::canCreate(...$args)) {
                return $class::create(...$args);
            }
        }

        throw new EngineNotFound($args[0] ?? null);
    }
}
