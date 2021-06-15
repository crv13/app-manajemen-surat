<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Disposisi extends Model
{
    protected $table = 'disposisi';
    protected $fillable = ['tujuan','isi','sifat','batas_waktu','catatan','users_id','suratmasuk_id'];

    public function smasuk()
    {
        return $this->belongsTo('App\SuratMasuk');
    }
}
