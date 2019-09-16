<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    protected $fillable = ['content'];

    //关联用户表
    public function user(){
        return $this->belongsTo(User::class);
    }
}
