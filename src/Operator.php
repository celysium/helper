<?php

namespace Celysium\Base;

use Illuminate\Database\Eloquent\Builder;

class Operator
{
    public static array $operators = [
        'like' => 'like',
        '%like' => 'prefixLike',
        'like%' => 'suffixLike',
        '%like%' => 'bothfixLike',
    ];

    public static function like(Builder $query, string $field, mixed $value): Builder
    {
        return $query->where($field, 'like', "$value");
    }

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