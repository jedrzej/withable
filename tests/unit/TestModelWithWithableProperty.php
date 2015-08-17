<?php

class TestModelWithWithableProperty extends TestModel
{
    protected $withable = ['relation1', 'relation2'];
}