<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workspace_members', function (Blueprint $table) {
            $table->integer('member_id')->autoIncrement();
            $table->integer('user_id');
            $table->integer('workspace_id');
            $table->string('role', 50)->default('member');
            $table->timestamp('joined_at')->useCurrent();
            $table->timestamps();

            $table->primary('member_id');
            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
            $table->foreign('workspace_id')->references('workspace_id')->on('workspace')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workspace_members');
    }
};
