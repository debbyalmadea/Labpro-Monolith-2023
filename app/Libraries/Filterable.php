<?php

namespace App\Libraries;

interface Filterable
{
    public function scopeFilter(FilterBuilderInterface $builder, array $parameters = []): FilterBuilderInterface;
    public static function builder($rows);
}