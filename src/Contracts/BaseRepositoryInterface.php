<?php
namespace Celysium\Helper\Contracts;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface BaseRepositoryInterface
{
    public function list(array $parameters, array $columns = ['*']): LengthAwarePaginator|Collection;

    public function find(int|string $id, $columns = ['*']): ?Model;

    public function findOrFail(int|string $id, $columns = ['*']): ?Model;

    public function findByField($field, $value, $columns = ['*']): ?Model;

    public function findOrFailByField($field, $value, $columns = ['*']): ?Model;

    public function store(array $parameters): Model;

    public function update(Model $model, array $parameters): Model;

    public function updateById(int|string $id, array $parameters): bool;

    public function destroy(Model $model): bool;

    public function destroyById(int|string $id): bool;
}
