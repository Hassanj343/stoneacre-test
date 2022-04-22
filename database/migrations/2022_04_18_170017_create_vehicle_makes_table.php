<?php

use App\Models\VehicleMake;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVehicleMakesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vehicle_makes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropColumn('make');
            $table->foreignIdFor(VehicleMake::class, 'make_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vehicle_makes');

        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropForeign('make_id');
            $table->string('make');
        });
    }
}
