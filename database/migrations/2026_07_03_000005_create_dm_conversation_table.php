<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dm_conversation', function (Blueprint $table) {
            $table->integer('conversation_id')->autoIncrement();
            $table->timestamps();

            $table->primary('conversation_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dm_conversation');
    }
};
