<?php

namespace Celysium\Helper\Contracts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface BaseServiceInterface
{
    public function index(array $parameters = []): LengthAwarePaginator|Collection;

    public function show(Model $model): Model;

    public function store(array $parameters): Model;

    public function update(Model $model, array $parameters): Model;

    public function destroy(Model $model): bool;
}
