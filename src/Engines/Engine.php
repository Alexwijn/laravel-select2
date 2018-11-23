<?php

namespace Alexwijn\Select2\Engines;

use Alexwijn\Select2\Contracts\Engine as EngineContract;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;

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
    protected $request;

    /**
     * Array of result columns/fields.
     *
     * @var array
     */
    protected $columns = [];

    /**
     * Json options when rendering.
     *
     * @var int
     */
    protected $jsonOptions;

    /**
     * Additional Json headers when rendering.
     *
     * @var int
     */
    protected $jsonHeaders;

    /**
     * Total records.
     *
     * @var int
     */
    protected $totalRecords = 0;

    /**
     * @var string
     */
    protected $value = 'id';

    /**
     * @var string
     */
    protected $label = 'text';

    /**
     * @var string
     */
    protected $group;

    /** @var array */
    protected $search;

    /** {@inheritdoc} */
    public static function create($source): EngineContract
    {
        return new static($source);
    }

    /** {@inheritdoc} */
    public function value(string $field): EngineContract
    {
        $this->value = $field;

        return $this;
    }

    /** {@inheritdoc} */
    public function label(string $field): EngineContract
    {
        $this->label = $field;

        return $this;
    }

    /** {@inheritdoc} */
    public function group(string $field): EngineContract
    {
        $this->group = $field;

        return $this;
    }

    /** {@inheritdoc} */
    public function search(...$fields): EngineContract
    {
        // Make sure we have arrays
        $fields = array_map('array_wrap', $fields);

        // Merge everything together
        $this->search = array_merge(...$fields);

        return $this;
    }

    /** {@inheritdoc} */
    public function toArray(): array
    {
        return $this->make()->getData(true);
    }

    /** {@inheritdoc} */
    public function toJson(array $headers = null, $options = 0): JsonResponse
    {
        if ($headers) {
            $this->jsonHeaders = $headers;
        }

        if ($options) {
            $this->jsonOptions = $options;
        }

        return $this->make();
    }

    /**
     * Render json response.
     *
     * @param array $data
     * @return \Illuminate\Http\JsonResponse
     */
    protected function render(array $data): JsonResponse
    {
        $currentPage = $this->request->get('page', 1);
        $totalPages = ceil($this->totalRecords / 15);

        $output = [
            'results' => $data,
            'pagination' => [
                'more' => $totalPages > $currentPage && $currentPage < $totalPages,
            ],
        ];

        if ($this->jsonHeaders === null) {
            $this->jsonHeaders = config('select2.json.headers', []);
        }

        if ($this->jsonOptions === null) {
            $this->jsonOptions = config('select2.json.options', 0);
        }

        return new JsonResponse(
            $output,
            200,
            $this->jsonHeaders,
            $this->jsonOptions
        );
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

    /**
     * Transform the data into a valid select2 response.
     *
     * @param \Illuminate\Support\Collection $data
     * @return array
     */
    protected function transform(Collection $data): array
    {
        if ($this->group !== null) {
            return $data->groupBy($this->group)->map(function (Collection $rows, string $label) {
                return [
                    'text' => $label ?: __('None'),
                    'children' => $this->collapse($rows),
                ];
            })->values()->toArray();
        }

        return $this->collapse($data)->toArray();
    }

    /**
     * Transform the data into a valid select2 row.
     *
     * @param \Illuminate\Support\Collection $data
     * @return \Illuminate\Support\Collection
     */
    protected function collapse(Collection $data): Collection
    {
        return $data->map(function ($row) {
            if ($row instanceof Arrayable) {
                $row = $row->toArray();
            }

            return array_merge($row, [
                'id' => data_get($row, $this->value),
                'text' => data_get($row, $this->label),
            ]);
        });
    }
}
