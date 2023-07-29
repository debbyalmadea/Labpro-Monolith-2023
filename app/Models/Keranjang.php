<?php

namespace App\Models;

use App\Libraries\Filterable;
use App\Libraries\FilterBuilder;
use App\Libraries\FilterBuilderInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Keranjang extends Model implements Filterable
{
    use HasFactory;

    protected $table = 'keranjang';

    protected $fillable = [
        'user_id',
        'barang_id',
        'jumlah_barang',
    ];

    public function scopeFilter(FilterBuilderInterface $builder, array $parameters = []): FilterBuilderInterface
    {
        return $builder->where('user_id', '=', $parameters['user_id']);
    }

    public static function builder($rows)
    {
        return (new FilterBuilder(new static(), $rows));
    }
}