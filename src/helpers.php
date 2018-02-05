<?php

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

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

if (! function_exists('morph_type')) {
    function morph_type($rateable)
    {
        if ($rateable instanceof Model) {
            $rateable = get_class($rateable);
        }

        return in_array($rateable, Relation::morphMap()) ? array_search($rateable, Relation::morphMap()) : $rateable;
    }
}
