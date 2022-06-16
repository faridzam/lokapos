<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePosProductStoreDesktopsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        if (!Schema::hasTable('pos_product_store_desktops')) {

            Schema::create('pos_product_store_desktops', function (Blueprint $table) {
                $table->id();
                $table->bigInteger('store_id');
                $table->bigInteger('category_id');
                $table->bigInteger('product_id');
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
        Schema::dropIfExists('pos_product_store_desktops');
    }
}
