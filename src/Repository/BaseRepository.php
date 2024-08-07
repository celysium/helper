<?php

namespace Celysium\Helper\Repository;

use Celysium\Helper\Contracts\BaseRepositoryInterface;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

/**
 * @property Model $model
 * @property static string $entity
 */
class BaseRepository implements BaseRepositoryInterface
{
    protected static string $entity;

    protected Model $model;

    /**
     * @param string $name
     * @param $value
     * @return void
     * @throws Exception
     */
    public function __set(string $name, $value): void
    {
        if ($name == 'entity') {
            $this->model($value);
        }
    }

    /**
     * @param string $value
     * @return Model
     * @throws Exception
     */
    public function model(string $value): Model
    {
        $model = new $value;

        if (!$model instanceof Model) {
            throw new Exception("Class $model must be an instance of Illuminate\\Database\\Eloquent\\Model");
        }

        return $this->model = $model;
    }

    public function list(array $parameters, callable $query = null, array $conditions = [], array $columns = ['*'], string $sort_by = null, string $sort_direction = null, string $export_type = null, int $per_page = null): LengthAwarePaginator|Collection
    {
        $parameters = array_merge($parameters, compact('sort_by', 'sort_direction', 'export_type', 'per_page'));

        $builder = $this->model->query();

        if ($query) {
            $builder = $query($builder);
        }

        $builder = $this->filterConditions($builder, $parameters, $conditions);

        $builder = $this->sort($builder, $parameters);

        return $this->export($builder, $parameters, $columns);
    }

    private function filterConditions(Builder $query, array $parameters, array $conditions): Builder
    {
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

    protected function sort(Builder $query, array $parameters): Builder
    {
        return $query
            ->orderBy($parameters['sort_by'] ?? $this->model->getKeyName(), $parameters['sort_direction'] ?? 'desc');
    }

    protected function export(Builder $query, array $parameters, array $columns = ['*']): Builder|Collection|LengthAwarePaginator|array
    {
        return match ($parameters['export_type']) {
            'builder' => $query->select($columns),
            'collection' => $query->get($columns),
            'array' => $query->get($columns)->toArray(),
            default => $query->paginate($parameters['per_page'] ?? $this->model->getPerPage(), $columns)
        };
    }

    public function find(int|string $id, $columns = ['*']): ?Model
    {
        return $this->model->query()->find($id, $columns);
    }

    public function findOrFail(int|string $id, $columns = ['*']): ?Model
    {
        return $this->model->query()->findOrFail($id, $columns);
    }

    public function findByField($field, $value, $columns = ['*']): ?Model
    {
        return $this->model->query()
            ->where($field, $value)->first($columns);
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
