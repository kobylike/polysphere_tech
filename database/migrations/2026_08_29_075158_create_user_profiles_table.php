<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('employee_id')->nullable()->unique();

            // FK added later in departments migration (departments doesn't exist yet)
            $table->unsignedBigInteger('department_id')->nullable();

            $table->date('hire_date')->nullable();
            $table->string('employment_type')->default('full-time');
            $table->boolean('is_employee')->default(false);
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('country_code', 10)->nullable();
            $table->string('city', 100)->nullable();

            $table->text('about_me')->nullable();
            $table->json('skills')->nullable();
            $table->json('education')->nullable();
            $table->json('social_links')->nullable();

            $table->string('position')->nullable();
            $table->string('gender', 20)->nullable();
            $table->boolean('is_featured_team')->default(false);
            $table->boolean('is_spotlight')->default(false);
            $table->integer('display_order')->default(0);

            $table->timestamps();

            $table->index('user_id');
            $table->index('is_featured_team');
            $table->index('department_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_profiles');
    }
};
