<?php

beforeEach(function () {
    [$this->category, $this->subcategory, $this->topic] = seoTree();
    $this->admin = seoAdmin();
});

it('renders the index with a missing-image badge for pages whose file does not exist', function () {
    $this->actingAs($this->admin, 'admin')
        ->get(route('admin.seo-pages.index'))
        ->assertOk()
        ->assertSee('Link Building Tools')
        ->assertSee('/seo/link-building/tools')
        ->assertSee('Missing image');
});

it('renders the create form with its key fields', function () {
    $this->actingAs($this->admin, 'admin')
        ->get(route('admin.seo-pages.create', ['parent_id' => $this->subcategory->id]))
        ->assertOk()
        ->assertSee('name="nav_label"', false)
        ->assertSee('name="featured_image_alt"', false)
        ->assertSee('id="faq-add"', false)
        ->assertSee('id="docx-import-button"', false)
        ->assertSee('SeoPage::ROUTED_CATEGORIES');
});

it('gives the content editor its own class and the image upload URL', function () {
    $html = $this->actingAs($this->admin, 'admin')
        ->get(route('admin.seo-pages.create'))
        ->assertOk()
        ->getContent();

    // `.summernote` would make custom.js initialise it with the generic toolbar (H1, colours, base64 images).
    expect($html)->toMatch('/<textarea class="form-control seo-page-editor" name="content" id="content"/')
        ->toContain('data-image-upload-url="'.route('admin.seo-pages.images.store').'"')
        ->not->toMatch('/<textarea[^>]*\bsummernote\b[^>]*id="content"/');
});

it('renders the edit form with the page values, escaping text fields', function () {
    $this->topic->update(['faq' => [['question' => 'Is <b>this</b> safe?', 'answer' => '<p>Yes.</p>']]]);

    $this->actingAs($this->admin, 'admin')
        ->get(route('admin.seo-pages.edit', $this->topic))
        ->assertOk()
        ->assertSee('name="nav_label"', false)
        ->assertSee('value="Link Building Tools"', false)
        ->assertSee('name="featured_image_alt"', false)
        ->assertSee('id="faq-add"', false)
        ->assertSee('id="docx-import-button"', false)
        ->assertSee('Is &lt;b&gt;this&lt;/b&gt; safe?', false)
        ->assertSee('&lt;p&gt;Yes.&lt;/p&gt;</textarea>', false);
});

function seoCheckboxChecked(string $html, string $field): bool
{
    preg_match('/<input[^>]*\bid="'.$field.'"[^>]*>/', $html, $match);

    expect($match)->not->toBeEmpty();

    return (bool) preg_match('/\schecked\b/', $match[0]);
}

it('refills unchecked toggles as unchecked after a failed submit', function () {
    $this->topic->update(['is_active' => true, 'show_toc' => true]);

    $html = $this->actingAs($this->admin, 'admin')
        ->withSession(['_old_input' => ['title' => 'x']])
        ->get(route('admin.seo-pages.edit', $this->topic))
        ->assertOk()
        ->getContent();

    expect(seoCheckboxChecked($html, 'is_active'))->toBeFalse()
        ->and(seoCheckboxChecked($html, 'show_toc'))->toBeFalse();
});

it('shows the saved toggles when there is no old input', function () {
    $this->topic->update(['is_active' => true]);

    $html = $this->actingAs($this->admin, 'admin')
        ->get(route('admin.seo-pages.edit', $this->topic))
        ->getContent();

    expect(seoCheckboxChecked($html, 'is_active'))->toBeTrue();
});

it('does not bring back saved FAQ rows the admin removed before a failed submit', function () {
    $this->topic->update(['faq' => [['question' => 'Zz saved question?', 'answer' => '<p>Yes.</p>']]]);

    $this->actingAs($this->admin, 'admin')
        ->withSession(['_old_input' => ['title' => 'x']])
        ->get(route('admin.seo-pages.edit', $this->topic))
        ->assertOk()
        ->assertDontSee('Zz saved question?');

    $this->flushSession();

    $this->actingAs($this->admin, 'admin')
        ->get(route('admin.seo-pages.edit', $this->topic))
        ->assertSee('Zz saved question?');
});

it('lists every validation error on the form', function () {
    $this->actingAs($this->admin, 'admin')
        ->from(route('admin.seo-pages.create'))
        ->followingRedirects()
        ->post(route('admin.seo-pages.store'), [
            'parent_id' => $this->category->id,
            'title' => '',
            'nav_label' => 'Zz Test Page',
            'content' => '',
            'featured_image_alt' => 'Some alt text',
        ])
        ->assertOk()
        ->assertSee('The title field is required.')
        ->assertSee('The content field is required.');
});

it('hides the edit preview when the saved image file is missing', function () {
    $this->actingAs($this->admin, 'admin')
        ->get(route('admin.seo-pages.edit', $this->topic))
        ->assertOk()
        ->assertSee('id="featured-image-preview" class="img-fluid rounded mt-2 d-none"', false)
        ->assertDontSee('missing-test-image.jpg');
});

it('hides add, edit and delete actions on the index from an admin who can only list', function () {
    $this->actingAs(seoAdmin(['seo-page-list']), 'admin')
        ->get(route('admin.seo-pages.index'))
        ->assertOk()
        ->assertSee('Link Building Tools')
        ->assertDontSee('title="Delete"', false)
        ->assertDontSee('title="Edit"', false)
        ->assertDontSee('title="Add child"', false)
        ->assertDontSee(route('admin.seo-pages.create'));
});

it('shows add, edit and delete actions on the index to an admin who can manage', function () {
    $this->actingAs($this->admin, 'admin')
        ->get(route('admin.seo-pages.index'))
        ->assertOk()
        ->assertSee('title="Delete"', false)
        ->assertSee('title="Edit"', false)
        ->assertSee('title="Add child"', false)
        ->assertSee(route('admin.seo-pages.create'));
});
