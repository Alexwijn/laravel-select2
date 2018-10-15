<?php

if (!function_exists('select2')) {
    /**
     * Helper to make a new Select2 instance from source.
     * Or return the factory if source is not set.
     *
     * @param mixed $source
     * @return \Alexwijn\Select2\Engines\Engine|\Alexwijn\Select2\Dropdown
     * @throws \Alexwijn\Select2\Exceptions\EngineNotFound
     */
    function select2($source = null)
    {
        if ($source === null) {
            return app('select2');
        }

        return app('select2')->make($source);
    }
}