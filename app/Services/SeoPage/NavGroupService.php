<?php

namespace App\Services\SeoPage;

use Illuminate\Support\Collection;

class NavGroupService
{
    /**
     * @param  Collection<int, object>  $pages  Objects with `nav_group` and `sort_order`.
     * @return list<array{group: string|null, pages: Collection}>
     */
    public function group(Collection $pages): array
    {
        $sorted = $pages->sortBy('sort_order')->values();

        $ungrouped = $sorted->filter(fn ($page) => blank($page->nav_group))->values();

        $grouped = $sorted
            ->reject(fn ($page) => blank($page->nav_group))
            ->groupBy('nav_group')
            ->sortBy(fn (Collection $items) => $items->min('sort_order'))
            ->map(fn (Collection $items, string $name) => ['group' => $name, 'pages' => $items->values()])
            ->values()
            ->all();

        if ($ungrouped->isEmpty()) {
            return $grouped;
        }

        return array_merge([['group' => null, 'pages' => $ungrouped]], $grouped);
    }
}
