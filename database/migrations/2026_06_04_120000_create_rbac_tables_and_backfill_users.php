<?php

use App\Support\PermissionRegistry;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->boolean('is_system')->default(false);
            $table->timestamps();
        });

        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('module');
            $table->string('action');
            $table->string('label');
            $table->timestamps();
        });

        Schema::create('permission_role', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id')->constrained()->cascadeOnDelete();
            $table->foreignId('permission_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['role_id', 'permission_id']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('role_id')->nullable()->after('Role')->constrained('roles')->nullOnDelete();
        });

        $now = Carbon::now();

        DB::table('permissions')->insert(array_map(
            fn (array $permission) => array_merge($permission, [
                'created_at' => $now,
                'updated_at' => $now,
            ]),
            PermissionRegistry::allPermissionRecords()
        ));

        $roles = [];

        foreach (PermissionRegistry::rolePresets() as $roleName => $preset) {
            $roleId = DB::table('roles')->insertGetId([
                'name' => $roleName,
                'slug' => \Illuminate\Support\Str::slug($roleName),
                'is_system' => $preset['system'] ?? false,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            $roles[$roleName] = $roleId;
        }

        $permissionIds = DB::table('permissions')->pluck('id', 'name');
        $pivotRows = [];

        foreach (array_keys(PermissionRegistry::rolePresets()) as $roleName) {
            foreach (PermissionRegistry::permissionNamesForPreset($roleName) as $permissionName) {
                $pivotRows[] = [
                    'role_id' => $roles[$roleName],
                    'permission_id' => $permissionIds[$permissionName],
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        DB::table('permission_role')->insert($pivotRows);

        $staffRoleId = $roles['Staff'] ?? null;
        $users = DB::table('users')->select('id', 'Role')->get();

        foreach ($users as $user) {
            $legacyRole = $user->Role ?: 'Staff';
            $roleId = $roles[$legacyRole] ?? $staffRoleId;
            $normalizedRole = array_key_exists($legacyRole, $roles) ? $legacyRole : 'Staff';

            DB::table('users')
                ->where('id', $user->id)
                ->update([
                    'role_id' => $roleId,
                    'Role' => $normalizedRole,
                ]);
        }
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('role_id');
        });

        Schema::dropIfExists('permission_role');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('roles');
    }
};
