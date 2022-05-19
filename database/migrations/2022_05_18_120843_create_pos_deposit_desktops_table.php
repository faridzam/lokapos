<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePosDepositDesktopsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('pos_deposit_desktops')) {
            Schema::create('pos_deposit_desktops', function (Blueprint $table) {

                $table->id();
                $table->bigInteger('pc_id');
                $table->bigInteger('cashier_id');
                $table->integer('pec100');
                $table->integer('pec50');
                $table->integer('pec20');
                $table->integer('pec10');
                $table->integer('pec5');
                $table->integer('pec2');
                $table->integer('pec1');
                $table->double('total');
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
        Schema::dropIfExists('pos_deposit_desktops');
    }
}
