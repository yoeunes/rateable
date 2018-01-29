<?php

namespace Yoeunes\Rateable\Tests\Stubs\Models;

use Illuminate\Database\Eloquent\Model;
use Yoeunes\Rateable\Traits\Rateable;

class Lesson extends Model
{
    use Rateable;

    protected $connection = 'testbench';

    protected $fillable = [
        'title',
        'subject',
    ];
}
