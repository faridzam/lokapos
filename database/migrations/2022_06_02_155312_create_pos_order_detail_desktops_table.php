<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePosOrderDetailDesktopsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('pos_order_detail_desktops')) {

            Schema::create('pos_order_detail_desktops', function (Blueprint $table) {
                $table->id();
                $table->string('no_invoice');
                $table->bigInteger('order_id');
                $table->bigInteger('product_id');
                $table->bigInteger('qty');
                $table->bigInteger('subtotal');
                $table->integer('discount');
                $table->bigInteger('specialPrice');
                $table->boolean('isActive')->default(true);
                $table->string('note')->nullable();
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
        Schema::dropIfExists('pos_order_detail_desktops');
    }
}
