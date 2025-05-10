<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('template_items', function (Blueprint $table) {
        $table->id();
        $table->foreignId('template_backpack_id')->constrained()->onDelete('cascade');
        $table->string('name'); 
        $table->integer('quantity')->default(1); 
        $table->timestamps();
    });
}

public function down()
{
    Schema::dropIfExists('template_items');
}

};
