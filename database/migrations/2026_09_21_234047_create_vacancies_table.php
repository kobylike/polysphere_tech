<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vacancies', function (Blueprint $table) {
            $table->id();

            $table->foreignId('department_id')
                ->nullable()
                ->constrained('departments')
                ->nullOnDelete();

            // Core content
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('summary', 500)->nullable();
            $table->longText('description');
            $table->longText('responsibilities')->nullable();
            $table->longText('requirements')->nullable();
            $table->longText('benefits')->nullable();

            // Classification (backed by PHP enums at the model level)
            $table->string('employment_type')->default('full_time');
            $table->string('experience_level')->default('mid');
            $table->string('workplace_type')->default('onsite');

            // Location
            $table->string('location')->nullable();
            $table->string('country')->nullable();

            // Compensation
            $table->decimal('salary_min', 12, 2)->nullable();
            $table->decimal('salary_max', 12, 2)->nullable();
            $table->string('salary_currency', 3)->default('USD');
            $table->boolean('is_salary_visible')->default(false);

            // Hiring workflow
            $table->unsignedInteger('positions_available')->default(1);
            $table->string('status')->default('draft'); // draft|published|closed|archived
            $table->boolean('is_featured')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->timestamp('closing_date')->nullable();

            // Analytics
            $table->unsignedBigInteger('views_count')->default(0);
            $table->unsignedBigInteger('applications_count')->default(0);

            // SEO
            $table->string('meta_title')->nullable();
            $table->string('meta_description', 500)->nullable();

            // Audit trail
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'published_at']);
            $table->index('employment_type');
            $table->index('experience_level');
            $table->index('workplace_type');
            $table->index('is_featured');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vacancies');
    }
};
