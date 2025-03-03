<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->text('description');
            $table->integer('price');
            $table->foreignId('condition_id')->nullable()->constrained('conditions')->onDelete('set null');
            $table->string('image_url');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('items');
    }
};
