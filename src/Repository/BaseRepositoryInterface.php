<?php
namespace Celysium\Helper\Repository;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface BaseRepositoryInterface
{
    public function index(Request $request, array $columns = ['*']): LengthAwarePaginator|Collection;

    public function conditions(Builder $query): array;

    public function query(Builder $query, Request $request): Builder;

    public function show(Model $model): Model;

    public function find(int|string $id): ?Model;

    public function findOrFail(int|string $id): ?Model;

    public function store(FormRequest $request): Model;

    public function update(Model $model, FormRequest $request): Model;

    public function updateById(int|string $id, FormRequest $request): ?Model;

    public function destroy(Model $model): bool;

    public function destroyById(int|string $id): bool;
}
