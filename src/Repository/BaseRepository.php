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


    public function __construct(protected Model $model)
    {
    }


    public function rules(): array
    {
        return [];
    }

    public function filters(Builder $query, array $parameters = [], array $columns = ['*']): Builder
    {
        $this->columns = $columns;
        $rules = $this->rules();
        foreach ($rules as $field => $condition) {
            if(array_key_exists($field, $parameters)) {
                if (is_callable($condition)) {
                    $query = $condition($query, $parameters[$field]);
                }
                else {
                    $query->where($field, $condition, $parameters[$field]);
                }
            }
        }
        return $query;
    }

    public function query(Builder $query, array $parameters): Builder
    {
        return $query;
    }

    public function index(array $parameters = [], array $columns = ['*']): LengthAwarePaginator|Collection
    {
        $query = $this->model->query();

        $query = $this->query($query, $parameters);

        $query = $this->filters($query, $parameters);

        $query->orderBy($parameters['sort_by'] ?? $this->model->getKeyName(), $parameters['sort_direction'] ?? 'desc');

        $columns = $columns == $this->columns ? $columns : $this->columns;

        if (isset($parameters['paginate']) && !$parameters['paginate'])
            return $query->get($columns);
        else
            return $query->paginate($parameters['per_page'] ?? $this->model->getPerPage(), $columns);
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

    public function updateById(int|string $id, array $parameters): ?Model
    {
        $result = $this->model->query()
                ->where($this->model->getKeyName(), $id)
                ->update($parameters) > 0;

        if($result === false)  {
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
