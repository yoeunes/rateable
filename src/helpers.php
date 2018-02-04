<?php

if (! function_exists('date_transformer')) {
    function date_transformer($value)
    {
        $transformers = collect(config('rateable.date-transformers'));

        if ($transformers->isEmpty() || ! $transformers->has((string) $value)) {
            return $value;
        }

        return $transformers->get($value);
    }
}
