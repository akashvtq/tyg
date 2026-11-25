<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    private array $names = ['seo-page-list', 'seo-page-manage'];

    public function up(): void
    {
        $now = now();

        foreach ($this->names as $name) {
            DB::table('permissions')->updateOrInsert(
                ['name' => $name, 'guard_name' => 'admin'],
                ['category' => 'site-essentials', 'created_at' => $now, 'updated_at' => $now]
            );
        }

        $roleId = DB::table('roles')->where(['name' => 'super-admin', 'guard_name' => 'admin'])->value('id');

        if ($roleId) {
            DB::table('permissions')->whereIn('name', $this->names)->where('guard_name', 'admin')->pluck('id')
                ->each(fn ($permissionId) => DB::table('role_has_permissions')->insertOrIgnore([
                    'permission_id' => $permissionId,
                    'role_id' => $roleId,
                ]));
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function down(): void
    {
        $ids = DB::table('permissions')->whereIn('name', $this->names)->where('guard_name', 'admin')->pluck('id');

        DB::table('role_has_permissions')->whereIn('permission_id', $ids)->delete();
        DB::table('model_has_permissions')->whereIn('permission_id', $ids)->delete();
        DB::table('permissions')->whereIn('id', $ids)->delete();

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
};
