<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePosLogActivityDesktopsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('pos_log_activity_desktops')) {

            Schema::create('pos_log_activity_desktops', function (Blueprint $table) {
                $table->id();
                $table->string('pic');
                $table->enum('type', ['read', 'create', 'update', 'delete', 'loginKasir', 'logoutKasir', 'loginDashboard', 'logoutDashboard', 'break', 'backToWork', 'closeOrder']);
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
        Schema::dropIfExists('pos_log_activity_desktops');
    }
}
