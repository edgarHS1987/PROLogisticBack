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

        Schema::create('zones', function(Blueprint $table){
            $table->id();
            $table->bigInteger('clients_id')->unsigned()->index();
            $table->string('name');
            $table->boolean('isLast')->default(true);
            $table->timestamps();
        });

        Schema::create('zones_zip_codes', function(Blueprint $table){
            $table->bigInteger('zones_id')->unsigned()->index();
            $table->bigInteger('zip_codes_id')->unsigned()->index();

            $table->foreign('zones_id')->references('id')->on('zones')->onDelete('cascade');
            $table->foreign('zip_codes_id')->references('id')->on('zip_codes')->onDelete('cascade');
        });

        Schema::create('zones_drivers', function(Blueprint $table){
            $table->bigInteger('zones_id')->unsigned()->index();
            $table->bigInteger('drivers_id')->unsigned()->index();
            $table->date('date');

            $table->foreign('zones_id')->references('id')->on('zones')->onDelete('cascade');
            $table->foreign('drivers_id')->references('id')->on('drivers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('zones_drivers');  
        Schema::dropIfExists('zones_zip_codes');
        Schema::dropIfExists('zones');
    }
};
