<?php

namespace App\Models;

use App\Libraries\ApiModel;
use App\Libraries\FilterBuilderInterface;

class Barang extends ApiModel
{
    public function scopeFilter(FilterBuilderInterface $builder, array $parameters = []): FilterBuilderInterface
    {
        if ($parameters['search']) {
            $builder->where('nama', 'contains', request('search'))
                ->orWhere('nama_perusahaan', 'contains', request('search'));
        }

        return $builder;
    }
}