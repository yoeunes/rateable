<?php

use Yoeunes\Rateable\Services\Raty;
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

if (! function_exists('raty_js')) {
    function raty_js($version = null, $src = null)
    {
        if (null === $version) {
            $version = config('rateable.raty.version');
        }

        if (null === $src) {
            $src = 'https://cdnjs.cloudflare.com/ajax/libs/raty/'.$version.'/jquery.raty.min.js';
        }

        return '<script type="text/javascript" src="'.$src.'"></script>';
    }
}

if (! function_exists('raty_css')) {
    function raty_css($version = null, $href = null)
    {
        if (null === $version) {
            $version = config('rateable.raty.version');
        }

        if (null === $href) {
            $href = 'https://cdnjs.cloudflare.com/ajax/libs/raty/'.$version.'/jquery.raty.min.css';
        }

        return '<link rel="stylesheet" type="text/css" href="'.$href.'">';
    }
}

if (! function_exists('jquery')) {
    function jquery($version = '3.3.1', $src = null)
    {
        if (null === $src) {
            $src = 'https://cdnjs.cloudflare.com/ajax/libs/jquery/'.$version.'/jquery.min.js';
        }

        return '<script type="text/javascript" src="'.$src.'"></script>';
    }
}

if (! function_exists('raty')) {
    /**
     * @param string $element
     *
     * @return Raty
     */
    function raty($element = '#raty')
    {
        return app('raty')->element($element);
    }
}
