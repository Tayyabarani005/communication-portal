<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('direct_message', function (Blueprint $table) {
            $table->integer('dm_message_id')->autoIncrement();
            $table->integer('conversation_id');
            $table->integer('sender_id');
            $table->integer('parent_id')->nullable();
            $table->text('msg_body')->nullable();
            $table->string('msg_type', 50)->default('text');
            $table->timestamp('sent_at')->useCurrent();
            $table->timestamps();

            $table->primary('dm_message_id');
            $table->foreign('conversation_id')->references('conversation_id')->on('dm_conversation')->onDelete('cascade');
            $table->foreign('sender_id')->references('user_id')->on('users')->onDelete('cascade');
            $table->foreign('parent_id')->references('dm_message_id')->on('direct_message')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('direct_message');
    }
};
