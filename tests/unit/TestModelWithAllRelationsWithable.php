<?php

class TestModelWithAllRelationsWithable extends TestModel
{
    protected $withable = ['relation1', '*'];
}