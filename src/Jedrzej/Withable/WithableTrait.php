<?php namespace Jedrzej\Withable;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Input;
use RuntimeException;

trait WithableTrait
{
    protected $withParameterName = 'with';

    /**
     * Add eager loaded relations.
     *
     * @param Builder $builder query builder
     * @param array $relations list of relations to be loaded
     */
    public function scopeWithRelations(Builder $builder, $relations = null)
    {
        $with = [];
        foreach ($this->getWithRelationsList($relations) as $relation) {
            if ($this->isWithableRelation($builder, $relation)) {
                $with[] = $relation;
            }
        }
        $builder->with($with);
    }

    protected function isWithableRelation(Builder $builder, $relation)
    {
        $withable = $this->_getWithableRelations($builder);

        return in_array($relation, $withable) || in_array('*', $withable);
    }

    protected function getWithRelationsList($relations = null)
    {
        return $relations ? (array)$relations : (array)Input::get($this->withParameterName, []);
    }

    /**
     * @return array list of relations that can be eagerly loaded
     */
    protected function _getWithableRelations(Builder $builder)
    {
        if (method_exists($builder->getModel(), 'getWithableRelations')) {
            return $builder->getModel()->getWithableRelations();
        }

        if (property_exists($builder->getModel(), 'withable')) {
            return $builder->getModel()->withable;
        }

        throw new RuntimeException(sprintf('Model %s must either implement getWithableRelations() or have $withable property set', get_class($builder->getModel())));
    }
}
