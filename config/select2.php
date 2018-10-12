<?php

return [
    /**
     * List of available builders for Select2.
     * This is where you can register your custom select2 builder.
     */
    'engines' => [
        'eloquent' => \Alexwijn\Select2\Engines\EloquentEngine::class,
        'query' => \Alexwijn\Select2\Engines\QueryEngine::class
    ],

    /**
     * Set this value to the name of the view (or partial) that
     * you want to prepend all dropdown scripts to.
     *
     * This can be a single view, or an array of views.
     * Example: 'footer' or ['footer', 'bottom']
     */
    'view' => 'layout.app',

    /**
     * Default html builder parameters.
     */
    'parameters' => [],

    /**
     * JsonResponse header and options config.
     */
    'json' => [
        'header' => [],
        'options' => 0,
    ],
];