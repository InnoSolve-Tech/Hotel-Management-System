<?php

namespace App\Support;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class PermissionRegistry
{
    public const ACTIONS = ['view', 'create', 'edit', 'delete'];

    public static function modules(): array
    {
        return [
            'dashboard' => ['label' => 'Dashboard'],
            'hotel' => ['label' => 'Hotels'],
            'room' => ['label' => 'Rooms'],
            'roomTransfer' => ['label' => 'Room Transfers'],
            'booking' => ['label' => 'Bookings'],
            'guest' => ['label' => 'Guests'],
            'employee' => ['label' => 'Employees'],
            'user' => ['label' => 'Users'],
            'role' => ['label' => 'Roles & Permissions'],
            'acount/ledger' => ['label' => 'Account Ledger'],
            'bank' => ['label' => 'Banks'],
            'bankLedger' => ['label' => 'Bank Ledger'],
            'income/category' => ['label' => 'Income Categories'],
            'income' => ['label' => 'Income'],
            'expense/category' => ['label' => 'Expense Categories'],
            'expense' => ['label' => 'Expense'],
            'balance' => ['label' => 'Balances'],
            'invoice' => ['label' => 'Invoices'],
            'taxSetting' => ['label' => 'Tax Settings'],
            'sms' => ['label' => 'SMS'],
            'payment' => ['label' => 'Payments'],
        ];
    }

    public static function routePrefixes(): array
    {
        return [
            'acount/ledger' => 'acount/ledger',
            'income/category' => 'income/category',
            'expense/category' => 'expense/category',
            'bankLedger' => 'bankLedger',
            'roomTransfer' => 'roomTransfer',
            'taxSetting' => 'taxSetting',
            'dashboard' => 'dashboard',
            'home' => 'dashboard',
            'hotel' => 'hotel',
            'room' => 'room',
            'booking' => 'booking',
            'guest' => 'guest',
            'employee' => 'employee',
            'user' => 'user',
            'role' => 'role',
            'bank' => 'bank',
            'income' => 'income',
            'expense' => 'expense',
            'balance' => 'balance',
            'invoice' => 'invoice',
            'sms' => 'sms',
            'payment' => 'payment',
        ];
    }

    public static function navigation(): array
    {
        return [
            [
                'title' => 'Dashboard',
                'path' => '/home',
                'module' => 'dashboard',
            ],
            [
                'title' => 'Operations',
                'children' => [
                    ['title' => 'Hotels', 'path' => '/hotel', 'module' => 'hotel'],
                    ['title' => 'Rooms', 'path' => '/room', 'module' => 'room'],
                    ['title' => 'Room Transfer', 'path' => '/roomTransfer', 'module' => 'roomTransfer'],
                    ['title' => 'Bookings', 'path' => '/booking', 'module' => 'booking'],
                    ['title' => 'Guests', 'path' => '/guest', 'module' => 'guest'],
                    ['title' => 'Employees', 'path' => '/employee', 'module' => 'employee'],
                ],
            ],
            [
                'title' => 'Finance',
                'children' => [
                    ['title' => 'Balances', 'path' => '/balance', 'module' => 'balance'],
                    ['title' => 'Invoices', 'path' => '/invoice', 'module' => 'invoice'],
                    ['title' => 'Banks', 'path' => '/bank', 'module' => 'bank'],
                    ['title' => 'Bank Ledger', 'path' => '/bankLedger', 'module' => 'bankLedger'],
                    ['title' => 'Account Ledger', 'path' => '/acount/ledger', 'module' => 'acount/ledger'],
                    ['title' => 'Tax Settings', 'path' => '/taxSetting', 'module' => 'taxSetting'],
                    [
                        'title' => 'Income',
                        'children' => [
                            ['title' => 'Categories', 'path' => '/income/category', 'module' => 'income/category'],
                            ['title' => 'Items', 'path' => '/income', 'module' => 'income'],
                        ],
                    ],
                    [
                        'title' => 'Expense',
                        'children' => [
                            ['title' => 'Categories', 'path' => '/expense/category', 'module' => 'expense/category'],
                            ['title' => 'Items', 'path' => '/expense', 'module' => 'expense'],
                        ],
                    ],
                    ['title' => 'Payments', 'path' => '/payment', 'module' => 'payment'],
                ],
            ],
            [
                'title' => 'Administration',
                'children' => [
                    ['title' => 'Users', 'path' => '/user', 'module' => 'user'],
                    ['title' => 'Roles & Permissions', 'path' => '/role', 'module' => 'role'],
                    ['title' => 'SMS', 'path' => '/sms', 'module' => 'sms'],
                ],
            ],
        ];
    }

    public static function rolePresets(): array
    {
        $full = array_fill_keys(array_keys(self::modules()), self::ACTIONS);

        return [
            'SuperAdmin' => [
                'system' => true,
                'permissions' => $full,
            ],
            'Admin' => [
                'system' => true,
                'permissions' => $full,
            ],
            'Manager' => [
                'system' => true,
                'permissions' => [
                    'dashboard' => ['view'],
                    'hotel' => self::ACTIONS,
                    'room' => self::ACTIONS,
                    'roomTransfer' => self::ACTIONS,
                    'booking' => self::ACTIONS,
                    'guest' => self::ACTIONS,
                    'employee' => self::ACTIONS,
                    'income/category' => ['view', 'create', 'edit'],
                    'income' => ['view', 'create', 'edit'],
                    'expense/category' => ['view', 'create', 'edit'],
                    'expense' => ['view', 'create', 'edit'],
                    'balance' => ['view', 'create', 'edit'],
                    'invoice' => ['view', 'create', 'edit'],
                    'payment' => ['view', 'create', 'edit'],
                ],
            ],
            'Cashier' => [
                'system' => true,
                'permissions' => [
                    'dashboard' => ['view'],
                    'hotel' => ['view'],
                    'room' => ['view'],
                    'booking' => self::ACTIONS,
                    'guest' => self::ACTIONS,
                    'invoice' => self::ACTIONS,
                    'payment' => self::ACTIONS,
                    'income/category' => ['view', 'create', 'edit'],
                    'income' => ['view', 'create', 'edit'],
                    'expense/category' => ['view', 'create', 'edit'],
                    'expense' => ['view', 'create', 'edit'],
                    'balance' => ['view', 'create', 'edit'],
                ],
            ],
            'Staff' => [
                'system' => true,
                'permissions' => [
                    'dashboard' => ['view'],
                    'hotel' => ['view'],
                    'room' => ['view'],
                    'roomTransfer' => ['view', 'create', 'edit'],
                    'booking' => ['view', 'create', 'edit'],
                    'guest' => ['view', 'create', 'edit'],
                ],
            ],
        ];
    }

    public static function permissionName(string $module, string $action): string
    {
        return sprintf('%s.%s', $module, $action);
    }

    public static function permissionLabel(string $module, string $action): string
    {
        $moduleLabel = Arr::get(self::modules(), $module . '.label', Str::headline($module));

        return sprintf('%s %s', Str::headline($action), $moduleLabel);
    }

    public static function allPermissionRecords(): array
    {
        $permissions = [];

        foreach (self::modules() as $module => $meta) {
            foreach (self::ACTIONS as $action) {
                $permissions[] = [
                    'name' => self::permissionName($module, $action),
                    'module' => $module,
                    'action' => $action,
                    'label' => self::permissionLabel($module, $action),
                ];
            }
        }

        return $permissions;
    }

    public static function permissionNamesForPreset(string $roleName): array
    {
        $preset = self::rolePresets()[$roleName] ?? ['permissions' => []];
        $permissionNames = [];

        foreach ($preset['permissions'] as $module => $actions) {
            foreach ($actions as $action) {
                $permissionNames[] = self::permissionName($module, $action);
            }
        }

        return array_values(array_unique($permissionNames));
    }

    public static function groupedPermissions(): array
    {
        $groups = [];

        foreach (self::modules() as $module => $meta) {
            $groups[] = [
                'module' => $module,
                'label' => $meta['label'],
                'permissions' => array_map(
                    fn (string $action) => [
                        'name' => self::permissionName($module, $action),
                        'action' => $action,
                        'label' => Str::headline($action),
                    ],
                    self::ACTIONS
                ),
            ];
        }

        return $groups;
    }

    public static function roleLabelOptions(): array
    {
        return array_map(
            fn (string $roleName) => [
                'name' => $roleName,
                'system' => self::rolePresets()[$roleName]['system'] ?? false,
            ],
            array_keys(self::rolePresets())
        );
    }

    public static function resolveModuleFromPath(string $path): ?string
    {
        $normalizedPath = trim(Str::of($path)->ltrim('/')->toString(), '/');

        if ($normalizedPath === '') {
            return null;
        }

        if (Str::startsWith($normalizedPath, 'api/v1/')) {
            $normalizedPath = Str::after($normalizedPath, 'api/v1/');
        }

        foreach (self::routePrefixes() as $prefix => $module) {
            if ($normalizedPath === $prefix || Str::startsWith($normalizedPath, $prefix . '/')) {
                return $module;
            }
        }

        return null;
    }

    public static function resolvePermissionFromRequest(Request $request): ?string
    {
        $module = self::resolveModuleFromPath($request->path());

        if (! $module) {
            return null;
        }

        $action = self::resolveActionFromRequest($request);

        if (! $action) {
            return null;
        }

        return self::permissionName($module, $action);
    }

    public static function resolveActionFromRequest(Request $request): ?string
    {
        $route = $request->route();
        $actionMethod = $route?->getActionMethod();

        if ($actionMethod) {
            $action = match ($actionMethod) {
                'index', 'show' => 'view',
                'create', 'store', 'send', 'order' => 'create',
                'edit', 'update', 'assignRole' => 'edit',
                'trash', 'restore', 'restoreAll', 'destroy', 'destroyAll',
                'deleteAll', 'forceDelete', 'forceDeleted', 'emptyTrash', 'emtyTrash' => 'delete',
                default => null,
            };

            if ($action) {
                return $action;
            }
        }

        $path = trim($request->path(), '/');

        if (Str::endsWith($path, '/create')) {
            return 'create';
        }

        if (Str::endsWith($path, '/edit') || Str::contains($path, 'assign/role')) {
            return 'edit';
        }

        if (
            Str::contains($path, ['/trash', '/restore', '/delete', 'emptyTrash', 'parmanently'])
            || Str::endsWith($path, ['trash', 'restoreAll', 'delete', 'emptytrash'])
        ) {
            return 'delete';
        }

        return match (strtoupper($request->method())) {
            'GET', 'HEAD' => 'view',
            'POST' => 'create',
            'PUT', 'PATCH' => 'edit',
            'DELETE' => 'delete',
            default => null,
        };
    }
}
