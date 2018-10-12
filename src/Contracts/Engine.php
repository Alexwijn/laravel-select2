<?php

namespace Alexwijn\Select2\Contracts;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;

/**
 * Alexwijn\Select2\Contracts\DropDown
 */
interface Engine
{
    /**
     * Can the select2 dropdown be created with these parameters.
     *
     * @param mixed $source
     * @return bool
     */
    public static function canCreate($source): bool;

    /**
     * Factory method, create and return an instance for the select2 dropdown.
     *
     * @param mixed $source
     * @return mixed
     */
    public static function create($source): Engine;

    /**
     * Create the Select2 dropdown.
     *
     * @return mixed
     */
    public function make(): JsonResponse;

    /**
     * Get paginated results.
     *
     * @return \Illuminate\Support\Collection
     */
    public function results(): Collection;
}
