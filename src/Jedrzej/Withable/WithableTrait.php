<?php namespace Jedrzej\Withable;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Request;
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
            list($relation, $scope) = $this->_parseRelation($relation);

            if ($this->isWithableRelation($builder, $relation)) {
                if ($scope) {
                    $with[$relation] = function ($query) use ($scope) {
                        $query->$scope();
                    };
                } else {
                    $with[] = $relation;
                }
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
        return $relations ? (array)$relations : (array)Request::get($this->withParameterName, []);
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

    /**
     * Parse relation string to extract relation name and optional scope
     *
     * @param $relation
     *
     * @return array [relation name, scope name]
     */
    protected function _parseRelation($relation)
    {
        $scope = null;
        if (strpos($relation, ':') !== false) {
            list($relation, $scope) = explode(':', $relation);
        }

        return [$relation, $scope];
    }
}
