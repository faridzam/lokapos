<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePosProductDesktopsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('pos_product_desktops')) {

            Schema::create('pos_product_desktops', function (Blueprint $table) {
                $table->id();
                $table->bigInteger('recipe_id');
                $table->string('product_code')->nullable();
                $table->string('image')->nullable();
                $table->string('name');
                $table->bigInteger('cost');
                $table->bigInteger('price');
                $table->boolean('isActive')->default(true);
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
        Schema::dropIfExists('pos_product_desktops');
    }
}
