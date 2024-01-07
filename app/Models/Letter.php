<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Letter extends Model
{
    use HasFactory;

    protected $fillable =[
        'letter_type_id',
        'letter_perihal',
        'recipients',
        'content',
        'attachment',
        'notulis',


    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function userLT() {
        return $this->belongsTo(User::class);
    }
    public function letterType()
    {
        return $this->belongsTo(Letter_type::class, 'letter_type_id', 'letter_code');
    }
    public function letter(){
        return $this->hasMany(Result::class);
    }
}
