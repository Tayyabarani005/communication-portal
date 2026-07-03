<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('message', function (Blueprint $table) {
            $table->integer('message_id')->autoIncrement();
            $table->integer('channel_id');
            $table->integer('sender_id');
            $table->integer('parent_id')->nullable();
            $table->text('msg_body')->nullable();
            $table->timestamp('sent_at')->useCurrent();
            $table->string('msg_type', 50)->default('text');
            $table->timestamps();

            $table->primary('message_id');
            $table->foreign('channel_id')->references('channel_id')->on('channel')->onDelete('cascade');
            $table->foreign('sender_id')->references('user_id')->on('users')->onDelete('cascade');
            $table->foreign('parent_id')->references('message_id')->on('message')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('message');
    }
};
