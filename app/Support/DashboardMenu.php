<?php

namespace App\Support;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class DashboardMenu
{
    public function for(?Authenticatable $user, ?Request $request = null): array
    {
        $request = $request ?: request();

        return collect(config('dashboard.menu', []))
            ->map(function (array $section) use ($user, $request) {
                $items = collect($section['items'] ?? [])
                    ->filter(fn (array $item) => $this->canSee($item, $user))
                    ->filter(fn (array $item) => $this->hasTarget($item))
                    ->map(function (array $item) use ($request) {
                        $activePatterns = $item['active'] ?? [$item['route'] ?? ''];

                        return [
                            'label' => $item['label'] ?? 'Elemento',
                            'description' => $item['description'] ?? null,
                            'icon' => $item['icon'] ?? 'fas fa-circle',
                            'url' => $this->resolveUrl($item),
                            'active' => collect($activePatterns)
                                ->filter()
                                ->contains(fn (string $pattern) => $request->routeIs($pattern)),
                        ];
                    })
                    ->values()
                    ->all();

                return [
                    'label' => $section['label'] ?? 'Sección',
                    'items' => $items,
                ];
            })
            ->filter(fn (array $section) => !empty($section['items']))
            ->values()
            ->all();
    }

    protected function canSee(array $item, ?Authenticatable $user): bool
    {
        $ability = $item['can'] ?? null;

        if (!$ability) {
            return true;
        }

        if (!$user || !method_exists($user, 'can')) {
            return false;
        }

        return $user->can($ability);
    }

    protected function hasTarget(array $item): bool
    {
        if (!empty($item['route'])) {
            return Route::has($item['route']);
        }

        return !empty($item['url']);
    }

    protected function resolveUrl(array $item): string
    {
        if (!empty($item['route']) && Route::has($item['route'])) {
            return route($item['route']);
        }

        return $item['url'] ?? '#';
    }
}
