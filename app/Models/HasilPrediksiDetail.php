<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HasilPrediksiDetail extends Model
{
    use HasFactory;

    protected $table = 'hasil_prediksi_detail';
    protected $primaryKey = 'id_hasil_prediksi_detail';

    protected $fillable = [
        'id_hasil_prediksi',
        'detail_minmax',
        'detail_mu',
        'detail_rules',
        'detail_sum_alpha',
        'detail_sum_alpha_z',
        'detail_z_akhir',
    ];

    protected $casts = [
        'detail_minmax' => 'array',
        'detail_mu'     => 'array',
        'detail_rules'  => 'array',
    ];

    public function hasilPrediksi()
    {
        return $this->belongsTo(HasilPrediksi::class, 'id_hasil_prediksi', 'id_hasil_prediksi');
    }
}
