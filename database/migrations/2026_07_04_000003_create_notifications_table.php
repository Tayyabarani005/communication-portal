<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('notifications');

        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id'); // recipient
            $table->integer('sender_id')->nullable(); // sender/triggerer
            $table->string('type'); // 'join_request', 'join_accepted', 'join_rejected', 'tag'
            $table->integer('workspace_id')->nullable();
            $table->integer('channel_id')->nullable();
            $table->integer('message_id')->nullable();
            $table->text('text');
            $table->boolean('is_seen')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
