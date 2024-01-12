<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('clients', function(Blueprint $table){
            $table->id();
            $table->string('name');
            $table->string('logo');
            $table->timestamps();
        });

        Schema::create('clients_warehouses', function(Blueprint $table){
            $table->bigInteger('clients_id')->unsigned()->index();
            $table->bigInteger('warehouses_id')->unsigned()->index();

            $table->foreign('clients_id')->references('id')->on('clients')->onDelete('cascade');
            $table->foreign('warehouses_id')->references('id')->on('warehouses')->onDelete('cascade');
        });

        Schema::create('clients_users', function(Blueprint $table){
            $table->bigInteger('clients_id')->unsigned()->index();
            $table->bigInteger('users_id')->unsigned()->index();

            $table->foreign('clients_id')->references('id')->on('clients')->onDelete('cascade');
            $table->foreign('users_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients_users');
        Schema::dropIfExists('clients_warehouses');
        Schema::dropIfExists('clients');
    }
};
