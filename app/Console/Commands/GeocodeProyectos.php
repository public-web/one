<?php

namespace App\Console\Commands;

use App\Models\BancoProyecto;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class GeocodeProyectos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'proyectos:geocode {--force : Force geocode even if coordinates exist}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Geocodificar proyectos usando Nominatim (OpenStreetMap)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $force = $this->option('force');

        $query = BancoProyecto::query();

        // Si no es force, solo geocodificar proyectos sin coordenadas
        if (!$force) {
            $query->where(function ($q) {
                $q->whereNull('latitude')
                  ->orWhereNull('longitude');
            });
        }

        $proyectos = $query->get();

        if ($proyectos->isEmpty()) {
            $this->info('No hay proyectos para geocodificar.');
            return Command::SUCCESS;
        }

        $this->info("Geocodificando {$proyectos->count()} proyectos...");

        $bar = $this->output->createProgressBar($proyectos->count());
        $bar->start();

        $success = 0;
        $failed = 0;

        foreach ($proyectos as $proyecto) {
            $address = $this->buildAddress($proyecto);

            if (empty($address)) {
                $failed++;
                $bar->advance();
                continue;
            }

            $coordinates = $this->geocode($address);

            if ($coordinates) {
                $proyecto->update([
                    'latitude' => $coordinates['lat'],
                    'longitude' => $coordinates['lon'],
                ]);
                $success++;
            } else {
                $failed++;
            }

            $bar->advance();

            // Respetar límite de Nominatim (1 request por segundo)
            sleep(1);
        }

        $bar->finish();
        $this->newLine(2);

        $this->info("✓ Geocodificados exitosamente: {$success}");
        if ($failed > 0) {
            $this->warn("✗ Fallidos: {$failed}");
        }

        return Command::SUCCESS;
    }

    /**
     * Build address string from proyecto data
     */
    private function buildAddress(BancoProyecto $proyecto): string
    {
        $parts = array_filter([
            $proyecto->tramo_direccion,
            $proyecto->barrio,
            $proyecto->localidad,
            'Colombia', // Añadir país para mejor precisión
        ]);

        return implode(', ', $parts);
    }

    /**
     * Geocode address using Nominatim
     */
    private function geocode(string $address): ?array
    {
        try {
            $response = Http::timeout(10)
                ->get('https://nominatim.openstreetmap.org/search', [
                    'q' => $address,
                    'format' => 'json',
                    'limit' => 1,
                    'addressdetails' => 1,
                ]);

            if ($response->successful() && count($response->json()) > 0) {
                $result = $response->json()[0];
                return [
                    'lat' => (float) $result['lat'],
                    'lon' => (float) $result['lon'],
                ];
            }

            return null;
        } catch (\Exception $e) {
            $this->error("Error geocodificando '{$address}': " . $e->getMessage());
            return null;
        }
    }
}
