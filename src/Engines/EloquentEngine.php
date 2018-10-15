<?php

namespace Alexwijn\Select2\Engines;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

/**
 * Alexwijn\Select2\EloquentEngine
 */
class EloquentEngine extends QueryEngine
{
    /**
     * EloquentDropdown constructor.
     *
     * @param Model|Builder $model
     */
    public function __construct($model)
    {
        // Retrieve the eloquent builder
        $builder = $model instanceof Builder ? $model : $model->getQuery();

        // Construct the variables
        parent::__construct($builder->getQuery());

        // Override the query builder with our eloquent builder
        $this->query = $builder;
    }

    /** {@inheritdoc} */
    public static function canCreate($source): bool
    {
        return $source instanceof Builder || $source instanceof Relation;
    }
}
