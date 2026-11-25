<?php

use App\Models\SeoPage;
use Illuminate\Http\UploadedFile;

const SEO_TEST_IMAGE_DIR = 'assets/general/images/seo-pages';

afterEach(function () {
    foreach (glob(base_path(SEO_TEST_IMAGE_DIR.'/zz-test-*')) ?: [] as $file) {
        @unlink($file);
    }
});

/**
 * Creates a real image file and returns its `featured_image` path (relative to assets/).
 */
function seoTestImage(): string
{
    $name = 'zz-test-'.uniqid().'.png';
    UploadedFile::fake()->image($name, 64, 48)->move(base_path(SEO_TEST_IMAGE_DIR), $name);

    return 'general/images/seo-pages/'.$name;
}

it('serves all three levels', function (string $url, string $h1) {
    seoTree();

    $this->get($url)->assertOk()->assertSee('<h1 class="seo-page__title">'.$h1.'</h1>', false);
})->with([
    'category' => ['/seo', 'SEO for Organic Growth'],
    'subcategory' => ['/seo/link-building', 'Link Building for SEO'],
    'topic' => ['/seo/link-building/tools', '27 Link Building Tools'],
]);

it('returns 404 for unknown, hidden or misplaced pages', function (string $url) {
    seoTree();
    SeoPage::factory()->create(['parent_id' => SeoPage::where('path', 'seo')->value('id'), 'slug' => 'draft', 'is_active' => false]);

    $this->get($url)->assertNotFound();
})->with([
    'unknown topic' => ['/seo/link-building/nope'],
    'inactive page' => ['/seo/draft'],
    'topic under the wrong subcategory' => ['/seo/draft/tools'],
    'fourth level' => ['/seo/link-building/tools/extra'],
    'other first segment' => ['/seox/link-building'],
]);

it('returns 404 for an active topic under an inactive subcategory', function () {
    [, $subcategory] = seoTree();
    $subcategory->update(['is_active' => false]);

    $this->get('/seo/link-building/tools')->assertNotFound();
});

it('renders the exact title tag, canonical and meta description', function () {
    [, , $topic] = seoTree();
    $topic->update([
        'meta_title' => str_repeat('Long title ', 8).'end',
        'meta_description' => 'Compare link building tools.',
    ]);

    $this->get('/seo/link-building/tools')
        ->assertSee('<title>'.str_repeat('Long title ', 8).'end</title>', false)
        ->assertSee('<link rel="canonical" href="'.url('seo/link-building/tools').'">', false)
        ->assertSee('<meta name="description" content="Compare link building tools.">', false)
        ->assertSee('<meta property="og:type" content="article" />', false);
});

it('outputs BreadcrumbList and Article JSON-LD, and FAQPage when there are questions', function () {
    [, , $topic] = seoTree();

    $this->get('/seo/link-building/tools')
        ->assertSee('"@type":"BreadcrumbList"', false)
        ->assertSee('"@type":"Article"', false)
        ->assertDontSee('"@type":"FAQPage"', false);

    $topic->update(['faq' => [['question' => 'Are free tools enough?', 'answer' => '<p>Sometimes.</p>']]]);

    $this->get('/seo/link-building/tools')
        ->assertSee('"@type":"FAQPage"', false)
        ->assertSee('<summary>Are free tools enough?</summary>', false);
});

it('escapes tags inside JSON-LD so content cannot close the script element', function () {
    [, , $topic] = seoTree();
    // Saved directly (bypassing the purifier) to prove the JSON encoding itself is safe.
    $topic->update(['faq' => [['question' => 'Q', 'answer' => '</script><b>x</b>']]]);

    $this->get('/seo/link-building/tools')
        ->assertSee('"text":"\u003C/script\u003E\u003Cb\u003Ex\u003C/b\u003E"', false)
        ->assertDontSee('"text":"</script>', false);
});

it('shows the breadcrumb with nav labels', function () {
    seoTree();

    $this->get('/seo/link-building/tools')->assertSeeInOrder(['Home', 'SEO', 'SEO Link Building', 'Link Building Tools']);
});

it('renders the featured image with its alt text', function () {
    [, , $topic] = seoTree();
    $topic->update(['featured_image' => seoTestImage(), 'featured_image_alt' => 'Dashboard showing referring domains']);

    $this->get('/seo/link-building/tools')
        ->assertSee('alt="Dashboard showing referring domains"', false)
        ->assertSee('width="64" height="48"', false)
        ->assertSee('fetchpriority="high"', false);
});

it('does not render a broken image when the featured image file is missing', function () {
    seoTree();

    $this->get('/seo/link-building/tools')
        ->assertOk()
        ->assertDontSee('fetchpriority="high"', false)
        ->assertDontSee('missing-test-image.jpg', false);

    // The subcategory's child card for the topic has no image either.
    $this->get('/seo/link-building')
        ->assertOk()
        ->assertSee('Link Building Tools')
        ->assertDontSee('missing-test-image.jpg', false);
});

it('shows the new body after an edit saved in the same second', function () {
    [, , $topic] = seoTree();
    $this->freezeTime();

    $topic->update(['content' => '<h2>Old Heading</h2><p>a</p>']);
    $this->get('/seo/link-building/tools')->assertSee('Old Heading');

    $topic->update(['content' => '<h2>New Heading</h2><p>b</p>']);
    $this->get('/seo/link-building/tools')
        ->assertSee('New Heading')
        ->assertDontSee('Old Heading');
});

it('points body images at this site image folder, wherever they were authored', function () {
    [, , $topic] = seoTree();
    $topic->update(['content' => '<h2>Images</h2><p>'
        .'<img src="http://staging.example/assets/general/images/seo-pages/a-body.png" alt="Chart">'
        .'<img src="/build-saas-links/assets/general/images/seo-pages/b-body.webp" alt="Table">'
        .'<img src="https://cdn.example.com/other/c.png" alt="Other">'
        .'</p>']);

    $this->get('/seo/link-building/tools')
        ->assertOk()
        ->assertSee('<img src="'.asset('general/images/seo-pages/a-body.png').'" alt="Chart">', false)
        ->assertSee('<img src="'.asset('general/images/seo-pages/b-body.webp').'" alt="Table">', false)
        ->assertSee('<img src="https://cdn.example.com/other/c.png" alt="Other">', false)
        ->assertDontSee('staging.example', false)
        ->assertDontSee('src="/build-saas-links/', false);
});

it('builds a table of contents from the H2s', function () {
    [, , $topic] = seoTree();
    $topic->update(['content' => '<h2>First Part</h2><p>a</p><h2>Second Part</h2><p>b</p>']);

    $this->get('/seo/link-building/tools')
        ->assertSee('<h2 id="first-part">First Part</h2>', false)
        ->assertSee('<a href="#second-part">Second Part</a>', false);
});

it('groups child cards and sidebar topics under their nav groups', function () {
    [, $subcategory, $tools] = seoTree();
    $tools->update(['nav_group' => 'Link Building Execution', 'sort_order' => 20]);
    SeoPage::factory()->create(['parent_id' => $subcategory->id, 'nav_label' => 'Backlinks', 'slug' => 'backlinks', 'sort_order' => 1]);
    SeoPage::factory()->create(['parent_id' => $subcategory->id, 'nav_label' => 'Guest Posting', 'slug' => 'guest-posting', 'nav_group' => 'Link Acquisition Methods', 'sort_order' => 10]);

    $this->get('/seo/link-building')->assertSeeInOrder([
        'Backlinks', 'Link Acquisition Methods', 'Guest Posting', 'Link Building Execution', 'Link Building Tools',
    ]);
});

it('lists only visible pages in the sitemap', function () {
    [, $subcategory] = seoTree();
    $subcategory->update(['is_active' => false]);

    $this->get('/sitemap-seo.xml')
        ->assertOk()
        ->assertHeader('Content-Type', 'application/xml; charset=UTF-8')
        ->assertSee('<loc>'.url('seo').'</loc>', false)
        ->assertDontSee('link-building', false);
});

it('lists only pages under routed categories in the sitemap', function () {
    seoTree();
    $ppc = SeoPage::factory()->create(['slug' => 'ppc', 'nav_label' => 'PPC']);
    SeoPage::factory()->create(['parent_id' => $ppc->id, 'slug' => 'google-ads']);

    $this->get('/sitemap-seo.xml')
        ->assertOk()
        ->assertSee('<loc>'.url('seo').'</loc>', false)
        ->assertDontSee(url('ppc'), false);
});
