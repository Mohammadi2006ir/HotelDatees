<?php
namespace App\Http\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Collection;
trait CanLoadRelationships
{
    public function loadRelationship(
        Model|QueryBuilder|EloquentBuilder|HasMany|Collection $for,
        ?array $relations = null
    ): Model|QueryBuilder|EloquentBuilder|HasMany|Collection {
        $relations = $relations ?? $this->relations ?? [];
        foreach ($relations as $relation) {
            $for->when(
                $this->shouldIncludeRelations($relation),
                //fn($q) => $for instanceof Model ? $for->loadMissing($relation) : $q->with($relation),
                function ($q) use ($for, $relation) {
                    if ($for instanceof Model || $for instanceof Collection) {
                        $for->loadMissing($relation);
                    } else {
                        $q->with($relation);
                    }
                }
            );
        }
        return $for;
    }

    protected function shouldIncludeRelations(string $relation): bool
    {
        $include = request('include');
        if (!$include) {
            return false;
        }
        $relations = array_map('trim', explode(',', $include));
        return in_array($relation, $relations);
    }
}
