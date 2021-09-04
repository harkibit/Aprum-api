<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Version extends Model
{
    //
    public $timestamps = false;

    protected $fillable = ['name', 'index'];

    public function language()
    {
        return $this->belongsTo(Language::class);
    }
}
