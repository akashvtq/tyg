<?php

use Illuminate\Support\Facades\DB;

it('runs feature tests against the dedicated test database', function () {
    expect(DB::connection()->getDatabaseName())->toBe('tygcrm_test');
    expect(setting('site_title'))->not->toBeNull();
});

it('renders the home page', function () {
    $this->get('/')->assertOk();
});
