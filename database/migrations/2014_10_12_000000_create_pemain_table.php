<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('pemain', function (Blueprint $table) {
            $table->id();
            $table->string('nama')->default('Pemain');
            $table->integer('pancasila_score')->default(0);
            $table->integer('nyawa')->default(3);
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('pemain');
    }
};