<?php

if (!function_exists('select2')) {
    /**
     * Helper to make a new Select2 instance from source.
     * Or return the factory if source is not set.
     *
     * @param mixed $source
     * @return \Alexwijn\Select\Select2Abstract|\Alexwijn\Select\Select
     */
    function select2($source = null)
    {
        if (is_null($source)) {
            return app('select2');
        }

        return app('select2')->make($source);
    }
}