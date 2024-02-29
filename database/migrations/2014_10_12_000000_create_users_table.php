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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('avatar')->nullable(); // Per la foto del profilo
            $table->string('bio')->nullable(); // Biografia dell'utente
            $table->date('date_of_birth')->nullable(); // Data di nascita
            $table->string('country')->nullable(); // Paese di residenza
            $table->string('city')->nullable(); // Città di residenza
            $table->json('roles')->nullable(); // Ruoli dell'utente
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });
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
