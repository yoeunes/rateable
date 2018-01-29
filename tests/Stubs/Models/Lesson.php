<?php

namespace Yoeunes\Rateable\Tests\Stubs\Models;

use Yoeunes\Rateable\Traits\Rateable;
use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    use Rateable;

    protected $connection = 'testbench';

    protected $fillable = [
        'title',
        'subject',
    ];

    protected $appends = [ 'average_rating' ];
}
