<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * @property int $id
 * @property string $tipo_elemento_civ_rupi
 * @property string $codigo_elemento
 * @property string|null $uso
 * @property string|null $area_elemento
 * @property string|null $localidad
 * @property string|null $upl
 * @property string|null $barrio
 * @property string|null $tramo_direccion
 * @property string|null $eje
 * @property string|null $inicio
 * @property string|null $fin
 * @property string|null $reserva
 * @property string|null $estado
 * @property string|null $id_contrato
 * @property float|null $latitude
 * @property float|null $longitude
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, DetalleBancoProyecto> $detalles
 * @property-read \Illuminate\Database\Eloquent\Collection<int, PreviabilizacionSocial> $previabilizaciones
 */
class BancoProyecto extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'banco_proyectos';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'tipo_elemento_civ_rupi',
        'codigo_elemento',
        'uso',
        'area_elemento',
        'localidad',
        'upl',
        'barrio',
        'tramo_direccion',
        'eje',
        'inicio',
        'fin',
        'reserva',
        'estado',
        'id_contrato',
        'latitude',
        'longitude',
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
     * Scope to search proyectos by codigo or other fields
     */
    public function scopeSearch($query, ?string $search)
    {
        if (empty($search)) {
            return $query;
        }

        return $query->where(function ($q) use ($search) {
            $q->where('codigo_elemento', 'like', "%{$search}%")
              ->orWhere('tipo_elemento_civ_rupi', 'like', "%{$search}%")
              ->orWhere('barrio', 'like', "%{$search}%")
              ->orWhere('localidad', 'like', "%{$search}%")
              ->orWhere('tramo_direccion', 'like', "%{$search}%");
        });
    }

    /**
     * Scope to filter by estado
     */
    public function scopeByEstado($query, ?string $estado)
    {
        if (empty($estado)) {
            return $query;
        }

        return $query->where('estado', $estado);
    }

    /**
     * Scope to filter by localidad
     */
    public function scopeByLocalidad($query, ?string $localidad)
    {
        if (empty($localidad)) {
            return $query;
        }

        return $query->where('localidad', $localidad);
    }

    /**
     * Scope to filter by id_contrato
     */
    public function scopeByContrato($query, ?string $idContrato)
    {
        if (empty($idContrato)) {
            return $query;
        }

        return $query->where('id_contrato', $idContrato);
    }

    /**
     * Get the detalles for the banco proyecto.
     */
    public function detalles(): HasMany
    {
        return $this->hasMany(DetalleBancoProyecto::class, 'codigo', 'codigo_elemento');
    }

    /**
     * Get the previabilizaciones for the banco proyecto.
     */
    public function previabilizaciones(): HasMany
    {
        return $this->hasMany(PreviabilizacionSocial::class, 'codigo', 'codigo_elemento');
    }

    /**
     * Configure activity logging
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'tipo_elemento_civ_rupi',
                'codigo_elemento',
                'uso',
                'area_elemento',
                'localidad',
                'estado',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => "Proyecto has been {$eventName}")
            ->useLogName('banco_proyectos');
    }
}
