<?php

namespace Alexwijn\Select2\Html;

/**
 * Alexwijn\Select2\Html\Builder
 */
class Builder
{
    /** @var \Alexwijn\Select2\Dropdown */
    protected $dropdown;

    /**
     * @var array
     */
    protected $parameters;

    /**
     * Builder constructor.
     *
     * @param \Alexwijn\Select2\Dropdown $dropdown
     */
    public function __construct(\Alexwijn\Select2\Dropdown $dropdown)
    {
        $this->dropdown = $dropdown;
    }

    /**
     * Customize the select2 options.
     *
     * @param array $parameters
     * @return \Alexwijn\Select2\Html\Builder
     */
    public function parameters(array $parameters): Builder
    {
        $this->parameters = $parameters;

        return $this;
    }

    /**
     * Render the select2 javascript element.
     *
     * @param string $element
     * @param array  $parameters
     * @return string
     */
    public function render(string $element, $parameters): string
    {
        try {
            return view('select2::javascript', [
                'element' => $element,
                'parameters' => collect($this->parameters)->merge($parameters)
            ])->render();
        } catch (\Throwable $e) {
            return '';
        }
    }
}
