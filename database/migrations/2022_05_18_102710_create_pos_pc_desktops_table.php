<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePosPcDesktopsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('pos_pc_desktops')) {
            Schema::create('pos_pc_desktops', function (Blueprint $table) {
                $table->id();
                $table->bigInteger('store_id');
                $table->string('name');
                $table->string('ip_address')->nullable();
                $table->string('cashier_printer')->nullable();
                $table->string('kitchen_printer')->nullable();
                $table->string('bar_printer')->nullable();
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
        Schema::dropIfExists('pos_pc_desktops');
    }
}
