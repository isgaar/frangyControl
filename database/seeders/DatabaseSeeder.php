<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $seeders = collect(glob(__DIR__ . '/*Seeder.php') ?: [])
            ->map(fn (string $path) => pathinfo($path, PATHINFO_FILENAME))
            ->reject(fn (string $class) => $class === class_basename(static::class))
            ->sort()
            ->map(fn (string $class) => __NAMESPACE__ . "\\{$class}")
            ->values()
            ->all();

        if ($seeders !== []) {
            $this->call($seeders);
        }
    }
}
