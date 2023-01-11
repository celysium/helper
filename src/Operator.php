<?php

namespace Celysium\BaseStructure;

use Illuminate\Database\Eloquent\Builder;

class Operator
{
    public static function prefixLike(Builder $query, string $field, mixed $value): Builder
    {
        return $query->where($field, 'like', "%$value");
    }

    public static function suffixLike(Builder $query, string $field, mixed $value): Builder
    {
        return $query->where($field, 'like', "$value%");
    }

    public static function bothfixLike(Builder $query, string $field, mixed $value): Builder
    {
        return $query->where($field, 'like', "%$value%");
    }
}