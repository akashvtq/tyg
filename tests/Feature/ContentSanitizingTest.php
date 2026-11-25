<?php

use App\Models\DepositMethod;
use App\Models\PageComponent;
use Illuminate\Http\UploadedFile;

// Admin-entered HTML is cleaned with HTML Purifier on save. These calls were
// commented out while config/purifier.php crashed every Purifier::clean() call.

const SANITIZE_PAYLOAD = '<p>Email <a href="mailto:hello@buildsaaslinks.com" target="_blank">hello@buildsaaslinks.com</a></p><script>alert(1)</script><p onclick="steal()">Terms</p>';

function assertSanitized(string $html): void
{
    expect($html)
        ->not->toContain('<script')
        ->not->toContain('alert(1)')
        ->not->toContain('onclick')
        ->toContain('href="mailto:hello@buildsaaslinks.com"')
        ->toContain('Terms');
}

function dynamicComponent(): PageComponent
{
    return PageComponent::create([
        'name' => 'zz-test-component-'.uniqid(),
        'icon' => 'general/images/zz-test-missing-icon.png',
        'section' => 'dynamic',
        'category' => 'dynamic',
        'type' => 'dynamic',
        'content' => json_encode(['en' => '<p>Before</p>']),
        'status' => 1,
        'with_modal' => 1,
    ]);
}

it('cleans component content on store and keeps mailto links', function () {
    $this->actingAs(seoAdmin(['component-list', 'component-manage']), 'admin')
        ->post(route('admin.page.component.store'), [
            'name' => 'zz-test-component-store',
            'icon' => UploadedFile::fake()->image('icon.png', 10, 10),
            'content' => SANITIZE_PAYLOAD,
            'status' => 1,
        ])
        ->assertRedirect();

    $component = PageComponent::where('name', 'zz-test-component-store')->firstOrFail();
    @unlink(base_path('assets/'.$component->icon));

    assertSanitized(json_decode($component->content, true)['en']);
});

it('cleans component content on update and keeps mailto links', function () {
    $component = dynamicComponent();

    $this->actingAs(seoAdmin(['component-list', 'component-manage']), 'admin')
        ->put(route('admin.page.component.update', $component->id), [
            'lang' => 'en',
            'name' => $component->name,
            'content' => SANITIZE_PAYLOAD,
            'status' => 1,
        ])
        ->assertRedirect();

    assertSanitized(json_decode($component->fresh()->content, true)['en']);
});

it('cleans deposit method payment details on update', function () {
    $method = DepositMethod::create([
        'name' => 'zz-test-manual',
        'type' => 'manual',
        'method_code' => 'zz-test-manual',
        'currency' => 'USD',
        'currency_symbol' => '$',
        'min_deposit' => 10,
        'max_deposit' => 100,
        'conversion_rate' => 1,
        'charge_type' => 'fixed',
        'charge' => 1,
        'status' => 0,
    ]);

    $this->actingAs(seoAdmin(['deposit-method-list', 'deposit-method-manage']), 'admin')
        ->put(route('admin.deposit.method.update', $method->id), [
            'type' => 'manual',
            'name' => 'zz-test-manual',
            'method_code' => 'zz-test-manual',
            'currency' => 'USD',
            'currency_symbol' => '$',
            'charge' => 1,
            'charge_type' => 'fixed',
            'conversion_rate_live' => 0,
            'conversion_rate' => 1,
            'min_deposit' => 10,
            'max_deposit' => 100,
            'fields' => [['name' => 'Transaction id', 'type' => 'text', 'validation' => 'required']],
            'receive_payment_details' => '<p>Pay to 000-000</p><script>alert(1)</script>',
            'status' => 0,
        ])
        ->assertRedirect();

    expect($method->fresh()->receive_payment_details)
        ->toContain('Pay to 000-000')
        ->not->toContain('<script')
        ->not->toContain('alert(1)');
});
