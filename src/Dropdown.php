<?php

namespace Alexwijn\Select2;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Alexwijn\Select2\DropDown
 */
class Dropdown
{
    /**
     * @var \Alexwijn\Select2\Html\Builder
     */
    private $htmlBuilder;

    /**
     * @var \Illuminate\Http\Request
     */
    private $request;

    /**
     * Display ajax response.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function ajax(): JsonResponse
    {
        $source = null;
        if (method_exists($this, 'query')) {
            $source = app()->call([$this, 'query']);
        }

        /** @var \Alexwijn\Select2\Engines\Engine $dropDown */
        $dropDown = app()->call([$this, 'dropDown'], compact('source'));

        return $dropDown->toJson();
    }

    /**
     * Get Select2 Request instance.
     *
     * @return \Illuminate\Http\Request
     */
    public function request(): Request
    {
        return $this->request ?: $this->request = resolve('request');
    }

    /**
     * Get Select2 Html Builder instance.
     *
     * @return \Alexwijn\Select2\Html\Builder
     */
    public function builder(): Html\Builder
    {
        return $this->htmlBuilder ?: $this->htmlBuilder = app('select2.html');
    }

    /**
     * Return script component of the dropdown.
     *
     * @return \Alexwijn\Select2\Html\Builder
     */
    public function html(): Html\Builder
    {
        return $this->builder()->parameters($this->getBuilderParameters());
    }

    /**
     * Get default builder parameters.
     *
     * @return array
     */
    protected function getBuilderParameters(): array
    {
        return config('select2.parameters');
    }
}
