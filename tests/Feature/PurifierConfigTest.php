<?php

use Mews\Purifier\Facades\Purifier;

// config/purifier.php's global custom_elements entry for <u> once had an invalid
// 5th argument, which made every Purifier::clean() call throw "Array to string conversion".
it('cleans HTML with the default profile without crashing and keeps <u>', function () {
    expect(Purifier::clean('<p><u>underlined</u></p><script>alert(1)</script>'))
        ->toContain('<u>underlined</u>')
        ->not->toContain('<script');
});
