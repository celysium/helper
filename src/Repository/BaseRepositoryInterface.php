<?php
namespace Celysium\Helper\Repository;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface BaseRepositoryInterface
{
    public function list(array $parameters, array $columns = ['*']): LengthAwarePaginator|Collection;

    public function conditions(Builder $query): array;

    public function query(Builder $query, array $parameters): Builder;

    public function find(int|string $id): ?Model;

    public function findOrFail(int|string $id): ?Model;

    public function store(array $parameters): Model;

    public function update(Model $model, array $parameters): Model;

    public function updateById(int|string $id, array $parameters): bool;

    public function destroy(Model $model): bool;

    public function destroyById(int|string $id): bool;
}
