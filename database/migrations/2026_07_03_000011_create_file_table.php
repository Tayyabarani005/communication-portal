<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('file', function (Blueprint $table) {
            $table->integer('file_id')->autoIncrement();
            $table->integer('attachable_id');
            $table->string('attachable_type', 255);
            $table->string('file_name', 255);
            $table->string('file_path', 255);
            $table->bigInteger('file_size')->nullable();
            $table->string('mime_type', 100)->nullable();
            $table->timestamps();
            $table->index(['attachable_type', 'attachable_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('file');
    }
};
