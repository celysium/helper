<?php

namespace Celysium\Helper\Repository;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class BaseRepository implements BaseRepositoryInterface
{
    public function __construct(protected Model $model)
    {
    }

    public function list(array $parameters, array $columns = ['*']): LengthAwarePaginator|Collection
    {
        $query = $this->model->query();

        $query = $this->query($query, $parameters);

        $query = $this->filterConditions($query, $parameters);

        $query = $this->sort($query, $parameters);

        return $this->export($query, $parameters, $columns);
    }

    public function conditions(Builder $query): array
    {
        return [];
    }

    private function filterConditions(Builder $query, array $parameters): Builder
    {
        $conditions = $this->conditions($query);
        if (empty($parameters) || empty($conditions) || empty($commons = array_intersect(array_keys($parameters), array_keys($conditions)))) {
            return $query;
        }
        foreach ($commons as $field) {
            $condition = $conditions[$field];
            $value = $parameters[$field];
            if (is_callable($condition)) {
                $query = $condition($value);
            } elseif ($condition == 'like') {
                $query->where($field, 'like', "%$value%");
            } else {
                $query->where($field, $condition, $value);
            }
        }
        return $query;
    }

    public function query(Builder $query, array $parameters): Builder
    {
        return $query;
    }

    protected function sort(Builder $query, array $parameters): Builder
    {
        return $query
            ->orderBy($parameters['sort_by'] ?? $this->model->getKeyName(), $parameters['sort_direction'] ?? 'desc');
    }

    protected function export(Builder $query, array $parameters, array $columns = ['*']): Builder|Collection|LengthAwarePaginator|array
    {
        return match ($parameters['export_type'] ?? null) {
            'builder' => $query->select($columns),
            'collection' => $query->get($columns),
            'array' => $query->get($columns)->toArray(),
            default => $query->paginate($parameters['per_page'] ?? $this->model->getPerPage(), $columns)
        };
    }

    public function find(int|string $id): ?Model
    {
        return $this->model->query()->find($id);
    }

    public function findOrFail(int|string $id): ?Model
    {
        return $this->model->query()->findOrFail($id);
    }

    public function store(array $parameters): Model
    {
        return $this->model->query()
            ->create($parameters);
    }

    public function update(Model $model, array $parameters): Model
    {
        $model->update($parameters);

        return $model->refresh();
    }

    public function updateById(int|string $id, array $parameters): bool
    {
        $result = $this->model->query()
            ->where($this->model->getKeyName(), $id)
            ->update($parameters);

        if ($result === 0) {
            throw (new ModelNotFoundException)->setModel(get_class($this->model), [$id]);
        }

        return $result;
    }

    public function destroy(Model $model): bool
    {
        return $model->delete();
    }

    public function destroyById(int|string $id): bool
    {
        return $this->model->query()
            ->where('id', $id)
            ->delete();
    }
}
