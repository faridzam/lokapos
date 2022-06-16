<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePosRawMaterialDesktopsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('pos_raw_material_desktops')) {

            Schema::create('pos_raw_material_desktops', function (Blueprint $table) {
                $table->id();
                $table->bigInteger('supplier_id');
                $table->string('name');
                $table->double('quantity', 16, 0);
                $table->enum('item_type', ['solid', 'liquid', 'gas']);
                $table->enum('unit', ['pcs', 'g', 'kg', 'mL', 'L', 'cc', 'tbsp', 'tsp', 'cm', 'mm']);
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
        Schema::dropIfExists('pos_raw_material_desktops');
    }
}
