<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Articulo extends Model
{
    use HasFactory;

    protected $fillable = [
        'titulo',
        'contenido',
        'user_id',
        'publicado',
    ];

    protected $casts = [
        'publicado' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
