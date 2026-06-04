<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class NavigationBuilder
{
    public static function forUser(?User $user, Request $request): array
    {
        $currentPath = '/' . trim($request->path(), '/');
        $currentModule = PermissionRegistry::resolveModuleFromPath($request->path());

        return array_values(array_filter(
            array_map(fn (array $item) => self::buildItem($item, $user, $currentPath, $currentModule), PermissionRegistry::navigation())
        ));
    }

    protected static function buildItem(array $item, ?User $user, string $currentPath, ?string $currentModule): ?array
    {
        $children = [];

        if (! empty($item['children'])) {
            $children = array_values(array_filter(
                array_map(fn (array $child) => self::buildItem($child, $user, $currentPath, $currentModule), $item['children'])
            ));
        }

        $module = $item['module'] ?? null;
        $canView = $module ? ($user?->hasPermission(PermissionRegistry::permissionName($module, 'view')) ?? false) : true;

        if (empty($children) && ! $canView) {
            return null;
        }

        if (! empty($item['children']) && empty($children)) {
            return null;
        }

        $path = $item['path'] ?? null;
        $active = $path ? self::pathMatches($item, $currentPath, $currentModule) : false;
        $open = $active;

        foreach ($children as $child) {
            $active = $active || ($child['active'] ?? false);
            $open = $open || ($child['open'] ?? false) || ($child['active'] ?? false);
        }

        return [
            'title' => $item['title'],
            'path' => $path,
            'module' => $module,
            'active' => $active,
            'open' => $open,
            'children' => $children,
        ];
    }

    protected static function pathMatches(array $item, string $currentPath, ?string $currentModule): bool
    {
        $itemPath = $item['path'];
        $normalizedItemPath = '/' . trim($itemPath, '/');
        $normalizedCurrentPath = '/' . trim($currentPath, '/');

        if ($normalizedItemPath === '/home' && $normalizedCurrentPath === '/dashboard') {
            return true;
        }

        if (($item['module'] ?? null) && $currentModule && $currentModule !== $item['module']) {
            return false;
        }

        return $normalizedCurrentPath === $normalizedItemPath
            || Str::startsWith($normalizedCurrentPath, $normalizedItemPath . '/');
    }
}
