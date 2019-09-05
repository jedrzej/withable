<?php

use Codeception\Specify;
use Codeception\AssertThrows;
use Codeception\TestCase\Test;

class WithableTraitTest extends Test
{
    use Specify, AssertThrows;

    public function testCriteria()
    {
        $this->specify("eager loading is applied when only one is given", function () {
            $this->assertCount(1, TestModelWithWithableMethod::withRelations('relation1')->getEagerLoads());
            $this->assertContains('relation1', array_keys(TestModelWithWithableMethod::withRelations('relation1')->getEagerLoads()));
            $this->assertNotContains('relation2', array_keys(TestModelWithWithableMethod::withRelations('relation1')->getEagerLoads()));
        });

        $this->specify("local scope is applied to a relation", function () {
            $this->assertCount(1, $eagerLoads = TestModelWithWithableMethod::withRelations('relation1:active')->getEagerLoads());
            $query = (new TestModelWithWithableMethod)->newQueryWithoutScopes();
            $eagerLoads['relation1']($query);

            $where = $query->getQuery()->wheres[0];
            $this->assertEquals('Basic', $where['type']);
            $this->assertEquals(true, $where['value']);
            $this->assertEquals('=', $where['operator']);
            $this->assertEquals('is_active', $where['column']);

        });

        $this->specify("eager loading is applied when array is given", function () {
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
            $this->assertThrows(RuntimeException::class, function() {
                TestModel::withRelations('relation1');
            });
        });

        $this->specify('* in withable relations list makes all relations loadable', function() {
            $this->assertCount(1, TestModelWithAllRelationsWithable::withRelations('relation1')->getEagerLoads());
            $this->assertCount(2, TestModelWithAllRelationsWithable::withRelations(['relation1', 'relation2'])->getEagerLoads());
            $this->assertCount(3, TestModelWithAllRelationsWithable::withRelations(['relation1', 'relation2', 'relation3'])->getEagerLoads());
            $this->assertCount(4, TestModelWithAllRelationsWithable::withRelations(['relation1', 'relation2', 'relation3', 'relation4'])->getEagerLoads());
        });
    }
}
