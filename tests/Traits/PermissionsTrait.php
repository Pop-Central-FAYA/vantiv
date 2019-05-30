<?php

namespace Tests\Traits;

use Spatie\Permission\Models\Permission;

trait PermissionsTrait
{
    public function permissionData()
    {
        factory(Permission::class)->create([
            'name' => 'create.inventory',
            'guard_name' => 'web'
        ]);

        factory(Permission::class)->create([
            'name' => 'update.user',
            'guard_name' => 'web'
        ]);

        factory(Permission::class)->create([
            'name' => 'update.super_admin',
            'guard_name' => 'web'
        ]);

        factory(Permission::class)->create([
            'name' => 'create.user',
            'guard_name' => 'web'
        ]);

        factory(Permission::class)->create([
            'name' => 'update.inventory',
            'guard_name' => 'web'
        ]);

        factory(Permission::class)->create([
            'name' => 'view.inventory',
            'guard_name' => 'web'
        ]);

        factory(Permission::class)->create([
            'name' => 'view.campaign',
            'guard_name' => 'web'
        ]);

        factory(Permission::class)->create([
            'name' => 'view.profile',
            'guard_name' => 'web'
        ]);

        factory(Permission::class)->create([
            'name' => 'view.user',
            'guard_name' => 'web'
        ]);

        factory(Permission::class)->create([
            'name' => 'view.rate_card',
            'guard_name' => 'web'
        ]);

        factory(Permission::class)->create([
            'name' => 'update.rate_card',
            'guard_name' => 'web'
        ]);

        factory(Permission::class)->create([
            'name' => 'view.discount',
            'guard_name' => 'web'
        ]);
        return Permission::all();
    }
}