<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('channel', function (Blueprint $table) {
            $table->integer('channel_id')->autoIncrement();
            $table->integer('workspace_id');
            $table->string('channel_name', 100);
            $table->boolean('is_private')->default(false);
            $table->timestamps();

            $table->primary('channel_id');
            $table->foreign('workspace_id')->references('workspace_id')->on('workspace')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('channel');
    }
};
