<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('users')) {

            Schema::create('users', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('username');
                $table->string('email')->unique();
                $table->timestamp('email_verified_at')->nullable();
                $table->string('password');
                $table->string('contact');
                $table->enum('role', ['engineer', 'it-admin', 'it-staff', 'fa-admin', 'fa-staff', 'ir-admin', 'ir-staff', 'fnb-admin', 'fnb-staff', 'common-user', 'viewer', 'man-fin', 'man-ir', 'man-fnb', 'man-it'])->nullable();
                $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
