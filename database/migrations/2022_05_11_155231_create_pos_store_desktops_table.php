<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePosStoreDesktopsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('pos_store_desktops')) {

            Schema::create('pos_store_desktops', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->enum('type', ['fnb', 'retail', 'others']);
                $table->enum('area', ['downtown', 'pesisir', 'balalantara', 'kamayayi', 'ararya', 'segara prada', 'others']);
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
        Schema::dropIfExists('pos_store_desktops');
    }
}
