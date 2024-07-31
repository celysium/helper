<?php

namespace Celysium\Helper\Service;

use Celysium\Helper\Contracts\BaseRepositoryInterface;
use Celysium\Helper\Contracts\BaseServiceInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class BaseService implements BaseServiceInterface
{
    public function __construct(protected BaseRepositoryInterface $repository)
    {
    }

    public function index(array $parameters= []): LengthAwarePaginator|Collection
    {
        return $this->repository->list($parameters);
    }

    public function show(Model $model): Model
    {
        return $model;
    }

    public function store(array $parameters): Model
    {
        return $this->repository->store($parameters);
    }

    public function update(Model $model, array $parameters): Model
    {
        return $this->repository->update($model, $parameters);
    }

    public function destroy(Model $model): bool
    {
        return $this->repository->destroy($model);
    }
}
