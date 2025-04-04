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
        Schema::create('trips', function (Blueprint $table) {
            $table->id();
            // Maybe we should write:
            //  $table->foreignId('user_id')->constrained()->cascadeOnDelete()
            // Which means then a user is deleted, their trips are also deleted
            $table->foreignId('user_id');
            $table->string('name');
            $table->string('description')->nullable();
            $table->string('destination');
            $table->date('start_date');
            $table->date('end_date');
            $table->string('url_photo')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trips');
    }
};
