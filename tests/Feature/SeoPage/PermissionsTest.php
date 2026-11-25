<?php

use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

it('creates the SEO page permissions for the admin guard', function () {
    foreach (['seo-page-list', 'seo-page-manage'] as $name) {
        $permission = Permission::findByName($name, 'admin');
        expect($permission->category)->toBe('site-essentials');
    }
});

it('grants both permissions to super-admin', function () {
    $role = Role::findByName('super-admin', 'admin');

    expect($role->hasPermissionTo('seo-page-list', 'admin'))->toBeTrue()
        ->and($role->hasPermissionTo('seo-page-manage', 'admin'))->toBeTrue();
});
