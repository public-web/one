<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * @property int $id
 * @property string $codigo
 * @property string|null $numero_radicado_entrada
 * @property \Illuminate\Support\Carbon|null $fecha_entrada
 * @property string|null $peticionario
 * @property string|null $asunto
 * @property string|null $numero_radicado_salida
 * @property \Illuminate\Support\Carbon|null $fecha_salida
 * @property string|null $observacion
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read BancoProyecto $bancoProyecto
 * @property-read \Illuminate\Database\Eloquent\Collection<int, DetalleDocumento> $documentos
 */
class DetalleBancoProyecto extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'detalle_banco_proyectos';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'codigo',
        'numero_radicado_entrada',
        'fecha_entrada',
        'peticionario',
        'asunto',
        'numero_radicado_salida',
        'fecha_salida',
        'observacion',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'fecha_entrada' => 'date',
            'fecha_salida' => 'date',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    /**
     * Get the banco proyecto that owns the detalle.
     */
    public function bancoProyecto(): BelongsTo
    {
        return $this->belongsTo(BancoProyecto::class, 'codigo', 'codigo_elemento');
    }

    /**
     * Get the documentos for the detalle.
     */
    public function documentos(): HasMany
    {
        return $this->hasMany(DetalleDocumento::class, 'detalle_banco_proyecto_id');
    }

    /**
     * Configure activity logging
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'codigo',
                'numero_radicado_entrada',
                'fecha_entrada',
                'peticionario',
                'asunto',
                'numero_radicado_salida',
                'fecha_salida',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => "Detalle proyecto has been {$eventName}")
            ->useLogName('detalle_banco_proyectos');
    }
}
