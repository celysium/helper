<?php

namespace Celysium\Base\Repository;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class BaseRepository implements BaseRepositoryInterface
{
    private array $columns;

    protected Builder $query;

    public function __construct(protected Model $model)
    {
    }

    public function index(array $parameters = [], array $columns = ['*']): LengthAwarePaginator|Collection
    {
        $this->query = $this->model->query();

        $this->query = $this->query($this->query, $parameters);

        $this->query = $this->filters($parameters);

        $this->query = $this->sort($parameters);

        return $this->export($parameters, $columns);
    }

    public function conditions(Builder $query): array
    {
        return [];
    }

    protected function filters(array $parameters = [], array $columns = ['*']): Builder
    {
        $this->columns = $columns;
        $rules = $this->conditions($this->query);
        foreach ($rules as $field => $condition) {
            if (array_key_exists($field, $parameters)) {
                if (is_callable($condition)) {
                    $this->query = $condition($parameters[$field]);
                } else {
                    $this->query->where($field, $condition, $parameters[$field]);
                }
            }
        }
        return $this->query;
    }

    public function query(Builder $query, array $parameters): Builder
    {
        return $query;
    }

    protected function sort(array $parameters): Builder
    {
        return $this->query
            ->orderBy($parameters['sort_by'] ?? $this->model->getKeyName(), $parameters['sort_direction'] ?? 'desc');
    }

    protected function export(array $parameters = [], array $columns = ['*']): Collection|LengthAwarePaginator|array
    {
        if ($columns != $this->columns) {
            $this->columns = $columns;
        }

        if (isset($parameters['paginate']) && !$parameters['paginate'])
            return $this->query->get($columns);
        else
            return $this->query->paginate($parameters['per_page'] ?? $this->model->getPerPage(), $columns);
    }

    public function show(Model $model): Model
    {
        return $model;
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

    public function updateById(int|string $id, array $parameters): Model
    {
        $result = $this->model->query()
            ->where($this->model->getKeyName(), $id)
            ->update($parameters);

        if ($result === 0) {
            throw (new ModelNotFoundException)->setModel(get_class($this->model), [$id]);
        }

        return $this->find($id);
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
