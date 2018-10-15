<?php
/**
 * @copyright Copyright (c) 2018, POS4RESTAURANTS BV. All rights reserved.
 * @internal  Unauthorized copying of this file, via any medium is strictly prohibited.
 */

namespace Alexwijn\Select2\Engines;

use Alexwijn\Select2\Contracts\Engine as EngineContract;
use Illuminate\Http\JsonResponse;

/**
 * Alexwijn\Select2\DropDown
 */
abstract class Engine implements EngineContract
{
    /**
     * Request object.
     *
     * @var \Illuminate\Http\Request
     */
    public $request;

    /**
     * Array of result columns/fields.
     *
     * @var array
     */
    protected $columns = [];

    /**
     * Total records.
     *
     * @var int
     */
    protected $totalRecords = 0;

    /** {@inheritdoc} */
    public static function create($source): EngineContract
    {
        return new static($source);
    }

    /**
     * Convert instance to array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return $this->make()->getData(true);
    }

    /**
     * Convert the object to its JSON representation.
     *
     * @param  int $options
     * @return \Illuminate\Http\JsonResponse
     */
    public function toJson($options = 0): JsonResponse
    {
        if ($options) {
            config('select2.json.options', $options);
        }

        return $this->make();
    }

    /**
     * Return an error json response.
     *
     * @param \Exception $exception
     * @return \Illuminate\Http\JsonResponse
     */
    protected function errorResponse(\Exception $exception): JsonResponse
    {
        return new JsonResponse([
            'results' => [],
            'pagination' => ['hasMore' => false],
            'error' => "Exception Message:\n\n" . $exception->getMessage(),
        ]);
    }
}
