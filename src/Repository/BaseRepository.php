<?php

namespace Celysium\BaseStructure\Repository;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
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

    public function filters(array $parameters = [], array $columns = ['*']): array
    {
        $this->columns = $columns;
        $rules = $this->rules();
        if(empty($rules)) {
            return [];
        }
        $conditions = [];
        foreach ($parameters as $key => $value) {
            if(isset($rules[$key])) {
                $conditions[] = [$key, $rules[$key], $value];
            }
        }
        return $conditions;
    }

    public function query(Builder $query): Builder
    {
        return $query;
    }

    public function index(array $parameters = []): LengthAwarePaginator|Collection
    {
        $query = $this->model->query();

        $query = $this->query($query);

        $conditions = $this->filters($parameters);
        if(! empty($conditions)) {
            $query->where($conditions);
        }

        $query->orderBy($parameters['sort_by'] ?? $this->model->getKeyName(), $parameters['sort_direction'] ?? 'desc');

        if (isset($parameters['paginate']) && !$parameters['paginate'])
            return $query->get($this->columns);
        else
            return $query->paginate($parameters['per_page'] ?? $this->model->getPerPage(), $this->columns);
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

        return $result ? $this->find($id) : null;
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
