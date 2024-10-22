<?php

namespace Vits\LaravelSimpleSearch\Tests\Support;

use Illuminate\Database\Eloquent\Model;
use Vits\LaravelSimpleSearch\SimpleSearch;

class TestModel extends Model
{
    use SimpleSearch;

    protected $table = 'test_models';

    protected $fillable = [
        'field1',
        'field2',
        'field3',
    ];
}
