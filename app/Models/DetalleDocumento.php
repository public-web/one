<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

/**
 * @property int $id
 * @property int $detalle_banco_proyecto_id
 * @property string $archivo_path
 * @property string $archivo_nombre
 * @property string|null $archivo_tipo
 * @property int|null $archivo_tamanio
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read DetalleBancoProyecto $detalle
 */
class DetalleDocumento extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'detalle_documentos';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'detalle_banco_proyecto_id',
        'archivo_path',
        'archivo_nombre',
        'archivo_tipo',
        'archivo_tamanio',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'archivo_url',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    /**
     * Get the detalle that owns the documento.
     */
    public function detalle(): BelongsTo
    {
        return $this->belongsTo(DetalleBancoProyecto::class, 'detalle_banco_proyecto_id');
    }

    /**
     * Get the URL for the archivo.
     */
    public function getArchivoUrlAttribute(): ?string
    {
        if (!$this->archivo_path) {
            return null;
        }

        return asset('storage/' . $this->archivo_path);
    }

    /**
     * Delete the archivo file from storage.
     */
    public function deleteArchivo(): bool
    {
        if ($this->archivo_path && Storage::disk('public')->exists($this->archivo_path)) {
            return Storage::disk('public')->delete($this->archivo_path);
        }

        return false;
    }

    /**
     * Get file size in human readable format.
     */
    public function getArchivoTamanioHumanoAttribute(): string
    {
        if (!$this->archivo_tamanio) {
            return '-';
        }

        $bytes = $this->archivo_tamanio;
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;

        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }
}
