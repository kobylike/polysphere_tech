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

            // Basic project information
            $table->string('title');
            $table->string('slug')->unique();
            $table->longText('content')->nullable();
            $table->text('excerpt')->nullable();

            // Service
            $table->foreignId('service_id')
                ->nullable()
                ->constrained('services')
                ->nullOnDelete();

            // Project images/media
            $table->string('featured_image')->nullable();
            $table->json('additional_images')->nullable();
            $table->string('thumbnail_image')->nullable();
            $table->string('video_url')->nullable();
            $table->string('video_file')->nullable();

            // Year range
            $table->integer('start_year')->nullable();
            $table->integer('end_year')->nullable();

            // Client/company information
            $table->string('client')->nullable();
            $table->string('company')->nullable();
            $table->string('location')->nullable();

            // The Challenge Of Project
            $table->longText('challenge_content')->nullable();
            $table->json('challenge_features')->nullable();
            $table->string('challenge_image')->nullable();

            // The Final View Of Project
            // Uses the existing thumbnail_image as the section image.
            $table->longText('final_view_content')->nullable();

            // Sidebar - Company File
            $table->string('attachment')->nullable();
            $table->string('attachment_original_name')->nullable();
            $table->unsignedBigInteger('attachment_size')->nullable();

            // Publishing
            $table->enum('status', [
                'draft',
                'published',
                'private',
                'pending',
                'trash'
            ])->default('draft');

            $table->enum('visibility', [
                'public',
                'password_protected',
                'private'
            ])->default('public');

            $table->timestamp('published_at')->nullable();

            // SEO
            $table->string('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->string('seo_keywords')->nullable();

            // Custom fields
            $table->json('custom_fields')->nullable();

            // Author
            $table->foreignId('author_id')
                ->constrained('users')
                ->onDelete('cascade');

            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
