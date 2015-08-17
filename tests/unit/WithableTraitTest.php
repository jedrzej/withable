<?php

use Codeception\Specify;
use Codeception\TestCase\Test;

class WithableTraitTest extends Test
{
    use Specify;

    public function testCriteria()
    {
        $this->specify("eager loading is applied when only one is given", function () {
            $this->assertCount(1, TestModelWithWithableMethod::withRelations('relation1')->getEagerLoads());
            $this->assertContains('relation1', array_keys(TestModelWithWithableMethod::withRelations('relation1')->getEagerLoads()));
            $this->assertNotContains('relation2', array_keys(TestModelWithWithableMethod::withRelations('relation1')->getEagerLoads()));
        });

        $this->specify("eager loading are applied when array is given", function () {
            $this->assertCount(2, TestModelWithWithableMethod::withRelations(['relation1', 'relation2'])->getEagerLoads());
            $this->assertContains('relation1', array_keys(TestModelWithWithableMethod::withRelations(['relation1', 'relation2'])->getEagerLoads()));
            $this->assertContains('relation2', array_keys(TestModelWithWithableMethod::withRelations(['relation1', 'relation2'])->getEagerLoads()));
            $this->assertNotContains('relation3', array_keys(TestModelWithWithableMethod::withRelations(['relation1', 'relation2'])->getEagerLoads()));
        });

        $this->specify("eager load is applied only to withable relations", function () {
            $this->assertCount(1, TestModelWithWithableMethod::withRelations('relation1')->getEagerLoads());
            $this->assertCount(2, TestModelWithWithableMethod::withRelations(['relation1', 'relation2'])->getEagerLoads());
            $this->assertCount(2, TestModelWithWithableMethod::withRelations(['relation1', 'relation2', 'relation3'])->getEagerLoads());
        });

        $this->specify('getWithableRelations is not required, if $withable property exists', function() {
            $this->assertCount(2, TestModelWithWithableProperty::withRelations(['relation1', 'relation2'])->getEagerLoads());
            $this->assertContains('relation1', array_keys(TestModelWithWithableProperty::withRelations(['relation1', 'relation2'])->getEagerLoads()));
            $this->assertContains('relation2', array_keys(TestModelWithWithableProperty::withRelations(['relation1', 'relation2'])->getEagerLoads()));
            $this->assertNotContains('relation3', array_keys(TestModelWithWithableProperty::withRelations(['relation1', 'relation2'])->getEagerLoads()));
        });

        $this->specify('model must implement getWithableAttributes() or have $withable property', function() {
            TestModel::withRelations('relation1');
        }, ['throws' => new RuntimeException]);

        $this->specify('* in withable relations list makes all relations loadable', function() {
            $this->assertCount(1, TestModelWithAllRelationsWithable::withRelations('relation1')->getEagerLoads());
            $this->assertCount(2, TestModelWithAllRelationsWithable::withRelations(['relation1', 'relation2'])->getEagerLoads());
            $this->assertCount(3, TestModelWithAllRelationsWithable::withRelations(['relation1', 'relation2', 'relation3'])->getEagerLoads());
            $this->assertCount(4, TestModelWithAllRelationsWithable::withRelations(['relation1', 'relation2', 'relation3', 'relation4'])->getEagerLoads());
        });
    }
}
