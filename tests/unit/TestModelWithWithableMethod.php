<?php

class TestModelWithWithableMethod extends TestModel
{
    /**
     * Returns list of loadable relations
     *
     * @return array
     */
    public function getWithableRelations()
    {
        return ['relation1', 'relation2'];
    }
}