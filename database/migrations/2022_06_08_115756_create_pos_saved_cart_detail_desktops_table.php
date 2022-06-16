<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePosSavedCartDetailDesktopsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('pos_saved_cart_detail_desktops')) {

            Schema::create('pos_saved_cart_detail_desktops', function (Blueprint $table) {
                $table->id();
                $table->string('no_invoice');
                $table->bigInteger('saved_cart_id');
                $table->bigInteger('product_id');
                $table->bigInteger('qty');
                $table->bigInteger('subtotal');
                $table->integer('discount');
                $table->bigInteger('specialPrice');
                $table->string('note');
                $table->boolean('isPrinted')->default(false);
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
        Schema::dropIfExists('pos_saved_cart_detail_desktops');
    }
}
