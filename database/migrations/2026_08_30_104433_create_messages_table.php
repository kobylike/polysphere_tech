<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();

            $table->foreignId('sender_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('receiver_id')->constrained('users')->onDelete('cascade');

            $table->foreignId('reply_to_id')
                ->nullable()
                ->constrained('messages')
                ->nullOnDelete();

            $table->text('body');
            $table->string('attachment_path')->nullable();
            $table->string('attachment_type')->nullable(); // image|video|audio|document|sticker
            $table->string('attachment_name')->nullable();
            $table->unsignedBigInteger('attachment_size')->nullable();

            $table->boolean('read')->default(false);
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('edited_at')->nullable();
            $table->softDeletes();

            $table->boolean('deleted_for_everyone')->default(false);
            $table->boolean('is_forwarded')->default(false);
            $table->boolean('is_pinned')->default(false);
            $table->json('metadata')->nullable();

            $table->timestamps();

            $table->index(['sender_id', 'receiver_id', 'created_at'], 'msg_sender_receiver_created_idx');
            $table->index(['receiver_id', 'read', 'created_at'], 'msg_receiver_read_created_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
