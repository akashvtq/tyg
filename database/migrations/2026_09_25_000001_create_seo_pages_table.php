<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seo_pages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->nullable()->constrained('seo_pages')->restrictOnDelete();
            $table->unsignedTinyInteger('depth');
            $table->string('title');
            $table->string('nav_label', 60);
            $table->string('nav_group', 60)->nullable();
            $table->string('slug', 60);
            $table->string('path', 191)->unique();
            $table->text('excerpt')->nullable();
            $table->longText('content');
            $table->json('faq')->nullable();
            $table->string('featured_image');
            $table->string('featured_image_alt');
            $table->foreignId('author_id')->nullable()->constrained('admins')->nullOnDelete();
            $table->string('meta_title')->nullable();
            $table->string('meta_description', 500)->nullable();
            $table->string('canonical_url')->nullable();
            $table->string('primary_keyword')->nullable();
            $table->string('cluster', 20)->nullable();
            $table->boolean('show_toc')->default(true);
            $table->boolean('show_cta')->default(true);
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->unique(['parent_id', 'slug']);
            $table->index(['parent_id', 'is_active', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seo_pages');
    }
};
