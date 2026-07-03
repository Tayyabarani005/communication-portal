<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workspace', function (Blueprint $table) {
            $table->integer('workspace_id')->autoIncrement();
            $table->string('name', 100);
            $table->text('description')->nullable();
            $table->timestamps();

            $table->primary('workspace_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workspace');
    }
};
