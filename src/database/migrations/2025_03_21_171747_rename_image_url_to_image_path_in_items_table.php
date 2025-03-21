<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameImageUrlToImagePathInItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('items', function (Blueprint $table) {
            $table->renameColumn('image_url', 'image_path');
        });
    }

    public function down()
    {
        Schema::table('items', function (Blueprint $table) {
            $table->renameColumn('image_path', 'image_url');
        });
    }

}
