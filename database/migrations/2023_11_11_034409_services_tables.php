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
        Schema::create('services', function(Blueprint $table){
            $table->id();
            $table->bigInteger('warehouses_id')->unsigned()->index();
            $table->bigInteger('clients_id')->unsigned()->index();
            $table->date('date');
            $table->time('time');
            $table->string('status', 100)->nullable();
            $table->string('guide_number')->nullable();
            $table->string('route_number')->nullable();
            $table->string('confirmation');
            $table->string('contact_name');
            $table->string('address');
            $table->string('zip_code', 6);
            $table->string('colony')->nullable();
            $table->string('state');
            $table->string('municipality');
            $table->string('phones');
            $table->boolean('assigned')->default(false);
            $table->string('latitude', 100)->nullable();
            $table->string('longitude', 100)->nullable();
            $table->string('full_address')->nullable();
            $table->timestamps();

            $table->foreign('warehouses_id')->references('id')->on('warehouses');
            $table->foreign('clients_id')->references('id')->on('clients');
        });

        Schema::create('services_drivers', function(Blueprint $table){
            $table->id();
            $table->bigInteger('services_id')->unsigned()->index();
            $table->bigInteger('drivers_id')->unsigned()->index();
            $table->date('date');
            $table->time('time');
            $table->string('status');
            $table->string('observations', 300)->nullable();
            $table->string('finish_location')->nullable();
            $table->boolean('isLast')->default(true);
            $table->timestamps();

            $table->foreign('services_id')->references('id')->on('services')->onDelete('cascade');
            $table->foreign('drivers_id')->references('id')->on('drivers')->onDelete('cascade');
        });

        Schema::create('services_drivers_exception', function(Blueprint $table){
            $table->id();
            $table->bigInteger('services_drivers_id')->unsigned()->index();
            $table->date('date');
            $table->time('hour');
            $table->string('reason');
            $table->timestamps();

            $table->foreign('services_drivers_id')->references('id')->on('services_drivers')->onDelete('cascade');
        });

        Schema::create('services_drivers_image', function(Blueprint $table){
            $table->id();
            $table->bigInteger('services_drivers_id')->unsigned()->index();
            $table->string('name', 100);
            $table->string('type', 100);
            $table->string('url', 150);
            $table->timestamps();

            $table->foreign('services_drivers_id')->references('id')->on('services_drivers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services_drivers_image');
        Schema::dropIfExists('services_drivers_exception');
        Schema::dropIfExists('services_drivers');
        Schema::dropIfExists('services');
    }
};
