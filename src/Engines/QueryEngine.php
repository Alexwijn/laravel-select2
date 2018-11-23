<?php

namespace Alexwijn\Select2\Engines;

use Illuminate\Database\Query\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * Alexwijn\Select2\QueryDropDown
 */
class QueryEngine extends Engine
{
    /**
     * Builder object.
     *
     * @var \Illuminate\Database\Query\Builder
     */
    protected $query;

    /**
     * Database connection used.
     *
     * @var \Illuminate\Database\Connection
     */
    protected $connection;

    /**
     * QueryEngine constructor.
     *
     * @param \Illuminate\Database\Query\Builder $builder
     */
    public function __construct(Builder $builder)
    {
        $this->query = $builder;
        $this->columns = $builder->columns;
        $this->request = app('request');
        $this->connection = $builder->getConnection();
    }

    /** {@inheritdoc} */
    public static function canCreate($source): bool
    {
        return $source instanceof Builder;
    }

    /** {@inheritdoc} */
    public function make(): JsonResponse
    {
        try {
            $this->prepareQuery();

            return $this->render(
                $this->transform($this->results())
            );
        } catch (\Exception $exception) {
            return $this->errorResponse($exception);
        }
    }

    /**
     * Get paginated results.
     *
     * @return \Illuminate\Support\Collection
     */
    public function results(): Collection
    {
        return $this->query->get();
    }

    /**
     * Counts current query.
     *
     * @return int
     */
    public function count(): int
    {
        $builder = $this->prepareCountQuery();
        $table = $this->connection->raw('(' . $builder->toSql() . ') count_row_table');
        return $this->connection->table($table)->setBindings($builder->getBindings())->count();
    }

    /**
     * Prepare query by executing count and paginate.
     */
    protected function prepareQuery(): QueryEngine
    {
        if ($this->totalRecords = $this->count()) {
            $this->filter();
            $this->paginate();
        }

        return $this;
    }

    /**
     * Prepare the count query.
     *
     * @return \Illuminate\Database\Query\Builder
     */
    protected function prepareCountQuery()
    {
        $builder = clone $this->query;
        if (!$this->isComplexQuery($builder)) {
            $row_count = $this->wrap('row_count');
            $builder->select($this->connection->raw("'1' as {$row_count}"));
            $builder->setBindings([], 'select');
        }

        return $builder;
    }

    /**
     * Check if builder query uses complex sql.
     *
     * @param \Illuminate\Database\Query\Builder $builder
     * @return bool
     */
    protected function isComplexQuery($builder): bool
    {
        return Str::contains(Str::lower($builder->toSql()), ['union', 'having', 'distinct', 'order by', 'group by']);
    }

    /**
     * Wrap column with DB grammar.
     *
     * @param string $column
     * @return string
     */
    protected function wrap($column): string
    {
        return $this->connection->getQueryGrammar()->wrap($column);
    }

    /**
     * Apply pagination.
     *
     * @return \Alexwijn\Select2\Engines\QueryEngine
     */
    protected function paginate(): QueryEngine
    {
        $this->query->forPage($this->request->get('page', 1), 15);

        return $this;
    }

    /**
     * Apply filter.
     *
     * @return \Alexwijn\Select2\Engines\QueryEngine
     */
    protected function filter(): QueryEngine
    {
        if ($term = $this->request->get('term')) {
            $fields = $this->search ?: [$this->label];
            $this->query->where(function ($query) use ($fields, $term) {
                foreach ($fields as $field) {
                    $this->regexColumnSearch($query, $field, $term);
                }
            });
        }

        return $this;
    }

    /**
     * Compile regex query column search.
     *
     * @param Builder $query
     * @param mixed   $column
     * @param string  $keyword
     */
    protected function regexColumnSearch($query, $column, $keyword): void
    {
        switch ($this->connection->getDriverName()) {
            case 'oracle':
                $sql = 'REGEXP_LIKE( LOWER(' . $column . ') , ?, \'i\' )';
                break;

            case 'pgsql':
                $sql = $column . ' ~* ? ';
                break;

            default:
                $sql = 'LOWER(' . $column . ') REGEXP ?';
                $keyword = Str::lower($keyword);
        }

        $query->orWhereRaw($sql, [$keyword]);
    }
}
