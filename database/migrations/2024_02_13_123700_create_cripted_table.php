<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCriptedTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cripted', function (Blueprint $table) {
            $table->bigInteger('user_id')->unsigned();
            $table->text('secureString');
            $table->bigInteger('tries')->unsigned();
            $table->boolean('locked')->default(false);
            $table->timestamp('last_login_attempt')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cripted');
    }
}
