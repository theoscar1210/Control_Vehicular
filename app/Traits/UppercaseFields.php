<?php

namespace App\Traits;

trait UppercaseFields
{
    public static function bootUppercaseFields(): void
    {
        static::saving(function ($model) {
            foreach ($model->uppercaseFields as $field) {
                if (!empty($model->{$field}) && is_string($model->{$field})) {
                    $model->{$field} = mb_strtoupper($model->{$field}, 'UTF-8');
                }
            }
        });
    }
}
