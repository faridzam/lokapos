<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePosCategoryDesktopsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('pos_category_desktops')) {

            Schema::create('pos_category_desktops', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->enum('type', ['makanan', 'minuman', 'non-konsumsi']);
                $table->boolean('isActive')->default('1')->change();
                $table->timestamps();
            });

        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pos_category_desktops');
    }
}
