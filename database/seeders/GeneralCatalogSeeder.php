<?php

namespace Database\Seeders;

use App\Models\DatosVehiculo;
use App\Models\TipoServicio;
use App\Models\TipoVehiculo;
use Illuminate\Database\Seeder;

class GeneralCatalogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->seedVehicleBrands([
            'Audi',
            'BMW',
            'Chevrolet',
            'Chirey',
            'Chrysler',
            'Dodge',
            'Fiat',
            'Ford',
            'GMC',
            'Honda',
            'Hyundai',
            'Infiniti',
            'Jeep',
            'Kia',
            'Mazda',
            'Mercedes-Benz',
            'MG',
            'Mini',
            'Mitsubishi',
            'Nissan',
            'Peugeot',
            'RAM',
            'Renault',
            'SEAT',
            'Subaru',
            'Suzuki',
            'Tesla',
            'Toyota',
            'Volkswagen',
            'Volvo',
        ]);

        $this->seedVehicleTypes([
            'Sedan',
            'Hatchback',
            'Coupe',
            'Convertible',
            'SUV',
            'Crossover',
            'Pickup',
            'Wagon',
            'Minivan',
            'Van',
            'Panel',
            'Camion',
            'Camioneta',
            'Motocicleta',
        ]);

        $this->seedServiceNames([
            'Cambio de aceite',
            'Afinacion',
            'Alineacion y balanceo',
            'Revision general',
            'Diagnostico',
            'Frenos',
            'Suspension',
            'Lavado',
            'Pulido',
            'Detallado interior',
            'Detallado exterior',
            'Cambio de llantas',
        ]);
    }

    private function seedVehicleBrands(array $brands): void
    {
        $existingBrands = DatosVehiculo::withTrashed()
            ->get()
            ->keyBy(fn (DatosVehiculo $brand) => $this->normalizeValue($brand->marca));

        foreach ($brands as $brandName) {
            $normalized = $this->normalizeValue($brandName);
            $brand = $existingBrands->get($normalized);

            if ($brand instanceof DatosVehiculo) {
                if ($brand->trashed()) {
                    $brand->restore();
                }

                if ($brand->marca !== $brandName) {
                    $brand->marca = $brandName;
                    $brand->save();
                }

                continue;
            }

            DatosVehiculo::create([
                'marca' => $brandName,
            ]);
        }
    }

    private function seedVehicleTypes(array $types): void
    {
        $existingTypes = TipoVehiculo::query()
            ->get()
            ->keyBy(fn (TipoVehiculo $type) => $this->normalizeValue($type->tipo));

        foreach ($types as $typeName) {
            $normalized = $this->normalizeValue($typeName);
            $type = $existingTypes->get($normalized);

            if ($type instanceof TipoVehiculo) {
                if ($type->tipo !== $typeName) {
                    $type->tipo = $typeName;
                    $type->save();
                }

                continue;
            }

            TipoVehiculo::create([
                'tipo' => $typeName,
            ]);
        }
    }

    private function seedServiceNames(array $services): void
    {
        $existingServices = TipoServicio::query()
            ->get()
            ->keyBy(fn (TipoServicio $service) => $this->normalizeValue($service->nombreServicio));

        foreach ($services as $serviceName) {
            $normalized = $this->normalizeValue($serviceName);
            $service = $existingServices->get($normalized);

            if ($service instanceof TipoServicio) {
                if ($service->nombreServicio !== $serviceName) {
                    $service->nombreServicio = $serviceName;
                    $service->save();
                }

                continue;
            }

            TipoServicio::create([
                'nombreServicio' => $serviceName,
            ]);
        }
    }

    private function normalizeValue(string $value): string
    {
        return strtolower(trim($value));
    }
}
