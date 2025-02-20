<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('images', function (Blueprint $table) {
            $table->id();
            $table->string('disk');
            $table->string('directory');
            $table->string('name');
            $table->string('path');
            $table->unsignedInteger('size')->nullable();
            $table->string('mime')->nullable();
            $table->string('alt')->nullable();
            $table->string('title')->nullable();
            $table->json('copyright')->nullable();
            $table->json('conversions')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('images');
    }
};
