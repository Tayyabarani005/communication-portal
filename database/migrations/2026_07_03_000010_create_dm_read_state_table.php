<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dm_read_state', function (Blueprint $table) {
            $table->integer('dm_read_state_id')->autoIncrement();
            $table->integer('conversation_id');
            $table->integer('user_id');
            $table->integer('last_read_message_id')->nullable();
            $table->timestamp('last_read_at')->nullable();
            $table->timestamps();
            $table->foreign('conversation_id')->references('conversation_id')->on('dm_conversation')->onDelete('cascade');
            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
            $table->foreign('last_read_message_id')->references('dm_message_id')->on('direct_message')->onDelete('set null');
            
            $table->unique(['conversation_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dm_read_state');
    }
};
