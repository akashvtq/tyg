<?php

use App\Services\SeoPage\TableOfContentsService;

it('adds ids to H2 and H3 headings and nests H3s under the preceding H2', function () {
    $html = '<p>Intro</p><h2>What Is Link Building?</h2><p>a</p><h3>Why It Matters</h3><h3>How It Works</h3><h2>Tools &amp; Software</h2>';

    $toc = (new TableOfContentsService)->build($html);

    expect($toc['html'])
        ->toContain('<h2 id="what-is-link-building">What Is Link Building?</h2>')
        ->toContain('<h3 id="why-it-matters">Why It Matters</h3>')
        ->toContain('<h2 id="tools-software">Tools &amp; Software</h2>')
        ->toContain('<p>Intro</p>');

    expect($toc['items'])->toHaveCount(2)
        ->and($toc['items'][0]['id'])->toBe('what-is-link-building')
        ->and($toc['items'][0]['text'])->toBe('What Is Link Building?')
        ->and(array_column($toc['items'][0]['children'], 'id'))->toBe(['why-it-matters', 'how-it-works'])
        ->and($toc['items'][1]['text'])->toBe('Tools & Software')
        ->and($toc['items'][1]['children'])->toBe([]);
});

it('de-duplicates ids', function () {
    $toc = (new TableOfContentsService)->build('<h2>FAQ</h2><h2>FAQ</h2><h2>FAQ</h2>');

    expect(array_column($toc['items'], 'id'))->toBe(['faq', 'faq-2', 'faq-3']);
});

it('gives an H3 before the first H2 an id but leaves it out of the list', function () {
    $toc = (new TableOfContentsService)->build('<h3>Lead</h3><p>x</p>');

    expect($toc['items'])->toBe([])
        ->and($toc['html'])->toContain('<h3 id="lead">Lead</h3>');
});

it('keeps non-ASCII text intact', function () {
    $toc = (new TableOfContentsService)->build('<h2>Café – “Quotes”</h2>');

    expect($toc['html'])->toContain('Café – “Quotes”');
});

it('returns empty content unchanged', function () {
    expect((new TableOfContentsService)->build(''))->toBe(['html' => '', 'items' => []]);
});
