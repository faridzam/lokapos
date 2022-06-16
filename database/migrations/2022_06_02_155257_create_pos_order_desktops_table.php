<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePosOrderDesktopsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('pos_order_desktops')) {

            Schema::create('pos_order_desktops', function (Blueprint $table) {
                $table->id();
                $table->string('no_invoice');
                $table->bigInteger('pc_id');
                $table->bigInteger('store_id');
                $table->bigInteger('cashier_id');
                $table->bigInteger('payment_id');
                $table->bigInteger('bill_amount');
                $table->bigInteger('pay_amount');
                $table->bigInteger('change_amount');
                $table->string('note')->nullable();
                $table->integer('tax')->default(0);
                $table->integer('discount')->default(0);
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
        Schema::dropIfExists('pos_order_desktops');
    }
}
