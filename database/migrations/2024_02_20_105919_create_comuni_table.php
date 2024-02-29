<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateComuniTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comuni', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('region');
            $table->string('province');
            $table->string('city')->nullable();
            $table->string('province_code')->nullable();
            $table->string('postal_code')->nullable();
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
        Schema::dropIfExists('comuni');
    }
}
