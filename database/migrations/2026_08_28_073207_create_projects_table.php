<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->longText('content')->nullable();
            $table->text('excerpt')->nullable();
            $table->foreignId('service_id')->nullable()->constrained('services')->nullOnDelete();
            $table->string('featured_image')->nullable();
            $table->json('additional_images')->nullable();
            $table->string('thumbnail_image')->nullable();
            $table->string('video_url')->nullable();
            $table->string('video_file')->nullable();

            // Year range
            $table->integer('start_year')->nullable();
            $table->integer('end_year')->nullable();
            $table->string('client')->nullable();
            $table->string('company')->nullable();
            $table->enum('status', ['draft', 'published', 'private', 'pending', 'trash'])->default('draft');
            $table->enum('visibility', ['public', 'password_protected', 'private'])->default('public');
            $table->timestamp('published_at')->nullable();
            $table->string('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->string('seo_keywords')->nullable();
            $table->json('custom_fields')->nullable();
            $table->foreignId('author_id')->constrained('users')->onDelete('cascade');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
