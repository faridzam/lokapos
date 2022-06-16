<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePosPaymentStoreDesktopsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('pos_payment_store_desktops')) {

            Schema::create('pos_payment_store_desktops', function (Blueprint $table) {
                $table->id();
                $table->bigInteger('payment_id');
                $table->bigInteger('store_id');
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
        Schema::dropIfExists('pos_payment_store_desktops');
    }
}
