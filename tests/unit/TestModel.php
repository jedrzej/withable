<?php

use Illuminate\Database\Eloquent\Model;
use Jedrzej\Withable\WithableTrait;

class TestModel extends Model
{
    use WithableTrait;

    protected function newBaseQueryBuilder()
    {
        return new TestBuilder;
    }
}