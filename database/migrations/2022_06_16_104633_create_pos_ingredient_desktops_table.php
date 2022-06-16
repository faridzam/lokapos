<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePosIngredientDesktopsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('pos_ingredient_desktops')) {

            Schema::create('pos_ingredient_desktops', function (Blueprint $table) {
                $table->id();
                $table->bigInteger('recipe_id');
                $table->bigInteger('raw_material_id');
                $table->double('quantity', 16, 0);
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
        Schema::dropIfExists('pos_ingredient_desktops');
    }
}
