<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('channel_read_state', function (Blueprint $table) {
            $table->integer('channel_read_state_id')->autoIncrement();
            $table->integer('channel_id');
            $table->integer('user_id');
            $table->integer('last_read_message_id')->nullable();
            $table->timestamp('last_read_at')->nullable();
            $table->timestamps();

            $table->primary('channel_read_state_id');
            $table->foreign('channel_id')->references('channel_id')->on('channel')->onDelete('cascade');
            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
            $table->foreign('last_read_message_id')->references('message_id')->on('message')->onDelete('set null');
            
            $table->unique(['channel_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('channel_read_state');
    }
};
