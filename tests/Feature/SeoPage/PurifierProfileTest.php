<?php

use Mews\Purifier\Facades\Purifier;

it('keeps tables, headings, lists and image alt text in seo_content', function () {
    $html = '<h2>Sub</h2><h3>Deeper</h3><ul><li>One</li></ul>'
        .'<div class="table-responsive"><table><thead><tr><th scope="col">A</th></tr></thead><tbody><tr><td colspan="2">1</td></tr></tbody></table></div>'
        .'<img src="https://example.com/chart.png" alt="Chart of referring domains">'
        .'<a href="https://example.com" target="_blank" rel="nofollow">Link</a>';

    $clean = Purifier::clean($html, 'seo_content');

    expect($clean)
        ->toContain('<h2>Sub</h2>')
        ->toContain('<h3>Deeper</h3>')
        ->toContain('<li>One</li>')
        ->toContain('<div class="table-responsive"><table>')
        ->toContain('<th scope="col">A</th>')
        ->toContain('<td colspan="2">1</td>')
        ->toContain('alt="Chart of referring domains"')
        ->toContain('rel="nofollow'); // HTML Purifier appends "noreferrer noopener" for target="_blank"
});

it('strips h1, inline styles, scripts and data: images in seo_content', function () {
    $html = '<h1>Title</h1><p style="color:red">Text</p><script>alert(1)</script><img src="data:image/png;base64,AAAA" alt="x">';

    $clean = Purifier::clean($html, 'seo_content');

    expect($clean)
        ->not->toContain('<h1')
        ->not->toContain('style=')
        ->not->toContain('<script')
        ->not->toContain('data:image')
        ->toContain('<p>Text</p>');
});

it('leaves the default profile unchanged', function () {
    expect(Purifier::clean('<table><tr><td>1</td></tr></table>'))->not->toContain('<table');
});

it('keeps target="_blank" and adds noopener to rel in seo_content', function () {
    // The global custom_attributes entry (['a', 'target', 'Enum#_blank,_self,_target,_top'])
    // replaces the target validator for every profile, so Attr.AllowedFrameTargets has no
    // effect here: any of _blank/_self/_target/_top passes through regardless of that setting.
    $clean = Purifier::clean('<a href="https://example.com" target="_blank">x</a>', 'seo_content');

    expect($clean)
        ->toContain('target="_blank"')
        ->toContain('rel="')
        ->toContain('noopener');
});

it('keeps strikethrough, subscript and superscript in seo_content', function () {
    $clean = Purifier::clean('<p><s>old</s> H<sub>2</sub>O x<sup>2</sup></p>', 'seo_content');

    expect($clean)->toBe('<p><s>old</s> H<sub>2</sub>O x<sup>2</sup></p>');
});
