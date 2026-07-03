<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->integer('task_id')->autoIncrement();
            $table->integer('channel_id');
            $table->integer('created_by');
            $table->integer('assigned_to')->nullable();
            $table->string('title', 255);
            $table->text('description')->nullable();
            $table->string('status', 50)->default('pending');
            $table->date('due_date')->nullable();
            $table->timestamps();

            $table->primary('task_id');
            $table->foreign('channel_id')->references('channel_id')->on('channel')->onDelete('cascade');
            $table->foreign('created_by')->references('user_id')->on('users')->onDelete('cascade');
            $table->foreign('assigned_to')->references('user_id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
