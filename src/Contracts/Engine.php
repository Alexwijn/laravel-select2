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

    /**
     * Change the default field for rendering the value.
     *
     * @param string $field
     * @return mixed
     */
    public function value(string $field): Engine;

    /**
     * Change the default field for rendering the label.
     *
     * @param string $field
     * @return mixed
     */
    public function label(string $field): Engine;

    /**
     * Assign a grouped field.
     *
     * @param string $field
     * @return mixed
     */
    public function group(string $field): Engine;

    /**
     * Override the fields that will be used when searching.
     *
     * @param array $fields
     * @return \Alexwijn\Select2\Contracts\Engine
     */
    public function search(...$fields): Engine;

    /**
     * Convert the object to its JSON representation.
     *
     * @param  array|null $headers
     * @param  int        $options
     * @return \Illuminate\Http\JsonResponse
     */
    public function toJson(array $headers = null, $options = 0): JsonResponse;

    /**
     * Convert instance to array.
     *
     * @return array
     */
    public function toArray(): array;
}
