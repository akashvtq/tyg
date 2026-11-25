<?php

use App\Services\SeoPage\NavGroupService;

function navPage(string $label, ?string $group, int $sort): object
{
    return (object) ['nav_label' => $label, 'nav_group' => $group, 'sort_order' => $sort];
}

it('puts ungrouped pages first, then groups ordered by their lowest sort order', function () {
    $pages = collect([
        navPage('Outreach', 'Execution', 20),
        navPage('Backlinks', null, 1),
        navPage('Guest Posting', 'Acquisition', 10),
        navPage('Tools', 'Execution', 21),
        navPage('Benefits', null, 2),
        navPage('Niche Edits', 'Acquisition', 11),
    ]);

    $groups = (new NavGroupService)->group($pages);

    expect(array_column($groups, 'group'))->toBe([null, 'Acquisition', 'Execution'])
        ->and($groups[0]['pages']->pluck('nav_label')->all())->toBe(['Backlinks', 'Benefits'])
        ->and($groups[1]['pages']->pluck('nav_label')->all())->toBe(['Guest Posting', 'Niche Edits'])
        ->and($groups[2]['pages']->pluck('nav_label')->all())->toBe(['Outreach', 'Tools']);
});

it('treats an empty string group as ungrouped', function () {
    $groups = (new NavGroupService)->group(collect([navPage('A', '', 0)]));

    expect($groups)->toHaveCount(1)->and($groups[0]['group'])->toBeNull();
});

it('returns no groups for no pages', function () {
    expect((new NavGroupService)->group(collect()))->toBe([]);
});
