<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('channel_user', function (Blueprint $table) {
            $table->integer('channel_user_id')->autoIncrement();
            $table->integer('channel_id');
            $table->integer('user_id');
            $table->timestamp('joined_at')->useCurrent();
            $table->timestamps();

            $table->primary('channel_user_id');
            $table->foreign('channel_id')->references('channel_id')->on('channel')->onDelete('cascade');
            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('channel_user');
    }
};
