<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('apartments', function (Blueprint $table) {
            $table->id();
            // $table->foreignId('owner_id')->nullable()
            //       ->constrained('users')
            //       ->onDelete('cascade');

            $table->string('description');
            $table->string('governorate');
            $table->string('city');
            $table->integer('price');
            $table->string('photo_path')->nullable();
            $table->boolean('is_available')->default(true);

            // التواريخ
            $table->timestamps();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('apartments');
    }
};
