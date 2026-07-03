<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pinned_message', function (Blueprint $table) {
            $table->integer('pin_id')->autoIncrement();
            $table->integer('pinnable_id');
            $table->string('pinnable_type', 255);
            $table->integer('pinned_by');
            $table->timestamps();

            $table->primary('pin_id');
            $table->foreign('pinned_by')->references('user_id')->on('users')->onDelete('cascade');
            $table->index(['pinnable_type', 'pinnable_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pinned_message');
    }
};
