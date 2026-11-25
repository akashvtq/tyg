<?php

namespace Database\Factories;

use App\Models\SeoPage;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<SeoPage>
 */
class SeoPageFactory extends Factory
{
    protected $model = SeoPage::class;

    public function definition(): array
    {
        $label = Str::title($this->faker->unique()->words(3, true));

        return [
            'parent_id' => null,
            'title' => $label.': A Complete Guide',
            'nav_label' => $label,
            'nav_group' => null,
            'slug' => Str::slug($label),
            'excerpt' => $this->faker->sentence(12),
            'content' => '<h2>Introduction</h2><p>'.$this->faker->paragraph().'</p><h2>Details</h2><p>'.$this->faker->paragraph().'</p>',
            'faq' => null,
            'featured_image' => 'general/images/seo-pages/missing-test-image.jpg',
            'featured_image_alt' => 'Illustration of '.Str::lower($label),
            'author_id' => null,
            'sort_order' => 0,
            'is_active' => true,
        ];
    }
}
