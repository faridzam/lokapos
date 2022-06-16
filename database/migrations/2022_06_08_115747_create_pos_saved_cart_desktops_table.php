<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePosSavedCartDesktopsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('pos_saved_cart_desktops')) {

            Schema::create('pos_saved_cart_desktops', function (Blueprint $table) {
                $table->id();
                $table->string('no_invoice');
                $table->bigInteger('pc_id');
                $table->bigInteger('store_id');
                $table->bigInteger('cashier_id');
                $table->bigInteger('bill_amount');
                $table->string('note');
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
        Schema::dropIfExists('pos_saved_cart_desktops');
    }
}
