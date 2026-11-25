<?php

use App\Models\Admin;
use App\Models\SeoPage;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
| Feature tests run against the `tygcrm_test` MySQL database (a copy of the
| dev database; see docs/superpowers/plans/2026-09-25-seo-pages.md, Task 1).
| Every test runs inside a transaction that is rolled back afterwards.
| Never run DDL (Schema::, TRUNCATE, ALTER) inside a test: MySQL commits it
| immediately, so it escapes the rollback and permanently changes tygcrm_test.
*/

uses(TestCase::class, DatabaseTransactions::class)
    ->beforeEach(function () {
        // Guard: never let a feature test touch a real database.
        throw_unless(
            DB::connection()->getDatabaseName() === 'tygcrm_test',
            new RuntimeException('Refusing to run feature tests outside tygcrm_test. If the config is cached, run: php artisan config:clear')
        );
    })
    ->in('Feature');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Helpers
|--------------------------------------------------------------------------
*/

/**
 * A slug unique to this test run, e.g. `zz-test-page-k3x9q2ab`. Tests that store an image use one,
 * because the file name comes from the slug: on Windows a file the previous test deleted can still
 * be locked, so reusing its name makes the next move fail. The zz-test- prefix keeps the
 * afterEach cleanup globs matching.
 */
function seoTestSlug(string $label = 'page'): string
{
    return 'zz-test-'.$label.'-'.Str::lower(Str::random(8));
}

/**
 * An active, verified admin without 2FA. The caller logs in with actingAs($admin, 'admin').
 */
function seoAdmin(array $permissions = ['seo-page-list', 'seo-page-manage']): Admin
{
    $admin = Admin::create([
        'first_name' => 'Test',
        'last_name' => 'Admin',
        'email' => 'seo-test-'.Str::lower(Str::random(10)).'@example.com',
        'password' => 'password',
        'status' => 1,
        'google2fa_enabled' => 0,
    ]);
    $admin->forceFill(['email_verified_at' => now()])->save();

    if ($permissions !== []) {
        $admin->givePermissionTo($permissions);
    }

    return $admin;
}

/**
 * Empties seo_pages (inside the test transaction) and builds /seo › /seo/link-building › /seo/link-building/tools.
 *
 * @return array{0: SeoPage, 1: SeoPage, 2: SeoPage}
 */
function seoTree(): array
{
    DB::table('seo_pages')->orderByDesc('depth')->delete();

    $category = SeoPage::factory()->create([
        'slug' => 'seo',
        'nav_label' => 'SEO',
        'title' => 'SEO for Organic Growth',
    ]);
    $subcategory = SeoPage::factory()->create([
        'parent_id' => $category->id,
        'slug' => 'link-building',
        'nav_label' => 'SEO Link Building',
        'title' => 'Link Building for SEO',
    ]);
    $topic = SeoPage::factory()->create([
        'parent_id' => $subcategory->id,
        'slug' => 'tools',
        'nav_label' => 'Link Building Tools',
        'title' => '27 Link Building Tools',
    ]);

    return [$category, $subcategory, $topic];
}
