<?php

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
        $totalPages = ceil($currentPage / 15);

        $output = [
            'results' => $data,
            'paginate' => [
                'hasMore' => $currentPage > $totalPages && $currentPage < $totalPages
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
     * @param array $data
     * @return array
     */
    protected function transform(array $data): array
    {
        return array_map(function ($row) {
            return [
                'id' => data_get($row, $this->value),
                'text' => data_get($row, $this->label)
            ];
        }, $data);
    }
}
