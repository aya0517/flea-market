<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::table('items', function (Blueprint $table) {
            $table->foreignId('buyer_id')->nullable()->constrained('users')->onDelete('set null')->after('user_id');
        });
    }

    public function down() {
        Schema::table('items', function (Blueprint $table) {
            $table->dropForeign(['buyer_id']);
            $table->dropColumn('buyer_id');
        });
    }
};
