<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Surat extends Model
{
    protected $table = 'surat';

    protected $fillable = [
        'id_pengirim',
        'id_penerima',
        'id_divisi',
        'id_judul',
        'perihal',
        'path',
        'filename',
    ];

    protected $casts = ['created_at' => 'datetime:d/m/Y'];

    public function to_user()
    {
        return $this->belongsTo('App\User', 'id_penerima');
    }

    public function divisi()
    {
        return $this->belongsTo('App\Model\Divisi', 'id_divisi');
    }

    public function judul()
    {
        return $this->belongsTo('App\Model\Judul', 'id_judul');
    }
}
