<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    //
    public $timestamps = false;

    protected $fillable = ['name', 'code', 'color'];

    public function versions()
    {
        return $this->hasMany(Version::class);
    }
}
